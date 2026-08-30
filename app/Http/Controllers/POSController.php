<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\CashShift;
use App\Models\ProductSerial;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function productSearch(Request $request)
    {
        $search = $request->input('query');
        if (empty($search)) {
            return response()->json([]);
        }

        $branchId = Auth::user()->branch_id ?? 1;

        // Search products by name, SKU, barcode, or IMEI serial
        $products = Product::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('serials', function ($q) use ($search) {
                        $q->where('serial_number', 'like', "%{$search}%")
                          ->where('status', 'in_stock');
                    });
            })
            ->with(['category', 'serials' => function ($q) {
                $q->where('status', 'in_stock');
            }, 'inventories' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->limit(25)
            ->get()
            ->map(function ($product) {
                $inv = $product->inventories->first();
                $product->stock_quantity = $inv ? $inv->quantity : 0;
                $product->category_name = $product->category ? $product->category->name : 'عام';
                return $product;
            });

        return response()->json($products);
    }

    public function customerSearch(Request $request)
    {
        $search = $request->input('query');
        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->get();

        return response()->json($customers);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|numeric|min:0.01',
            'cart.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,wallet,card,split,credit',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;

        // Verify open shift
        $shift = CashShift::where('user_id', $user->id)->where('status', 'open')->first();
        if (!$shift) {
            $shift = CashShift::where('branch_id', $branchId)->where('status', 'open')->first();
        }
        if ($user->role === 'cashier' && !$shift) {
            return response()->json(['success' => false, 'message' => 'عفواً، وردية الكاشير مغلقة. يرجى فتح وردية كاشير أولاً للبيع.'], 422);
        }

        // Verify wallet exists for the branch
        $wallet = Wallet::where('branch_id', $branchId)->where('is_active', true)->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'لا توجد خزينة نشطة للفرع لاستقبال المبلغ.'], 422);
        }

        $cart = $request->input('cart');
        $overallDiscount = floatval($request->input('discount', 0));
        $paymentMethod = $request->input('payment_method', 'cash');
        $notes = $request->input('notes');

        $subtotal = 0;
        $itemsData = [];

        foreach ($cart as $item) {
            $product = Product::findOrFail($item['id']);
            $price = floatval($item['selling_price'] ?? $product->selling_price);
            $qty = floatval($item['qty']);
            $itemDiscount = floatval($item['discount'] ?? 0);
            $total = max(0, ($price * $qty) - $itemDiscount);
            $subtotal += $total;

            $itemsData[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'discount' => $itemDiscount,
                'total' => $total,
                'serials' => $item['serials'] ?? [],
            ];
        }

        $taxRate = floatval(setting('tax_percentage', 0));
        $tax = $subtotal * ($taxRate / 100);
        $total = max(0, ($subtotal + $tax) - $overallDiscount);
        $paidAmount = $paymentMethod === 'credit' ? 0 : $total;

        DB::beginTransaction();
        try {
            // 1. Create Sale invoice record
            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'branch_id' => $branchId,
                'cashier_id' => $user->id,
                'customer_id' => $request->input('customer_id'),
                'cash_shift_id' => $shift ? $shift->id : null,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'discount_amount' => $overallDiscount,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'notes' => $notes,
            ]);

            // 2. Loop items, record sale items, deduct inventory, allocate serials
            foreach ($itemsData as $data) {
                $product = $data['product'];
                $qty = $data['qty'];

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => ceil($qty),
                    'unit_price' => $data['price'],
                    'discount' => $data['discount'],
                    'total' => $data['total'],
                ]);

                // Deduct stock count from branch inventory
                $inv = Inventory::where('branch_id', $branchId)->where('product_id', $product->id)->first();
                if ($inv) {
                    $inv->decrement('quantity', $qty);
                } else {
                    Inventory::create([
                        'branch_id' => $branchId,
                        'product_id' => $product->id,
                        'quantity' => -$qty,
                    ]);
                }

                // If device requires serials (IMEIs)
                if ($product->has_serials) {
                    $allocatedSerials = [];

                    // 1. Mark explicitly scanned/selected serials as sold
                    if (!empty($data['serials'])) {
                        foreach ($data['serials'] as $serialNum) {
                            if (empty($serialNum)) continue;
                            $pSerial = ProductSerial::where('product_id', $product->id)
                                ->where('serial_number', $serialNum)
                                ->where('status', 'in_stock')
                                ->first();

                            if ($pSerial) {
                                $pSerial->update([
                                    'status' => 'sold',
                                    'sale_item_id' => $saleItem->id,
                                ]);
                                $allocatedSerials[] = $pSerial->id;
                            }
                        }
                    }

                    // 2. Auto-allocate remaining available serials from in_stock if main barcode was scanned
                    $neededCount = (int)ceil($qty) - count($allocatedSerials);
                    if ($neededCount > 0) {
                        $autoSerials = ProductSerial::where('product_id', $product->id)
                            ->where('branch_id', $branchId)
                            ->where('status', 'in_stock')
                            ->whereNotIn('id', $allocatedSerials)
                            ->limit($neededCount)
                            ->get();

                        foreach ($autoSerials as $pSerial) {
                            $pSerial->update([
                                'status' => 'sold',
                                'sale_item_id' => $saleItem->id,
                            ]);
                        }
                    }
                }
            }

            // 3. Log Money Wallet Transaction
            $wallet->increment('balance', $total);
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $total,
                'balance_after' => $wallet->balance,
                'category' => 'sale',
                'description' => 'إيداع قيمة الفاتورة مبيعات رقم: ' . $sale->invoice_number,
                'reference_id' => $sale->id,
                'reference_type' => 'Sale',
                'performed_by' => $user->id,
            ]);

            // 4. Loyalty points accrual (1 point per 100 EGP)
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $pointsAccrued = floor($total / 100);
                    $customer->increment('loyalty_points', $pointsAccrued);
                    $customer->increment('total_purchases', $total);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'تم تسجيل عملية البيع وإصدار الفاتورة بنجاح.',
                'invoice' => $sale->invoice_number,
                'sale_id' => $sale->id,
                'invoice_url' => route('sales.invoice', $sale->id),
                'auto_print' => setting('auto_print_receipt', '0') == '1',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إتمام العملية: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get real-time status of user active shift
     */
    public function shiftStatus(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;

        $shift = CashShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$shift && $user->role !== 'cashier') {
            $shift = CashShift::where('branch_id', $branchId)
                ->where('status', 'open')
                ->first();
        }

        $branchUsers = \App\Models\User::where('branch_id', $branchId)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'role']);

        if (!$shift) {
            return response()->json([
                'has_open_shift' => false,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'branch_users' => $branchUsers,
            ]);
        }

        $salesQuery = Sale::where('cash_shift_id', $shift->id)->where('status', 'completed');
        $salesCount = (clone $salesQuery)->count();
        $cashSales = (clone $salesQuery)->where('payment_method', 'cash')->sum('total');
        $cardSales = (clone $salesQuery)->whereIn('payment_method', ['card', 'wallet', 'split'])->sum('total');
        $creditSales = (clone $salesQuery)->where('payment_method', 'credit')->sum('total');
        $totalSales = (clone $salesQuery)->sum('total');

        $expectedCash = floatval($shift->opening_balance) + floatval($cashSales);

        return response()->json([
            'has_open_shift' => true,
            'shift' => [
                'id' => $shift->id,
                'user_name' => $shift->user->name ?? $user->name,
                'branch_name' => $shift->branch->name ?? '',
                'opening_time' => $shift->opening_time ? $shift->opening_time->format('Y-m-d h:i A') : '',
                'opening_balance' => floatval($shift->opening_balance),
                'cash_sales' => floatval($cashSales),
                'card_sales' => floatval($cardSales),
                'credit_sales' => floatval($creditSales),
                'total_sales' => floatval($totalSales),
                'sales_count' => $salesCount,
                'expected_cash' => $expectedCash,
                'notes' => $shift->notes,
            ],
            'branch_users' => $branchUsers,
        ]);
    }

    /**
     * Open new shift for user
     */
    public function openShift(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;

        $existing = CashShift::where('user_id', $user->id)->where('status', 'open')->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'لديك وردية مفتوحة بالفعل.'], 422);
        }

        $shift = CashShift::create([
            'user_id' => $user->id,
            'branch_id' => $branchId,
            'opening_time' => now(),
            'opening_balance' => floatval($request->input('opening_balance', 0)),
            'expected_balance' => floatval($request->input('opening_balance', 0)),
            'status' => 'open',
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم فتح الوردية بنجاح.',
            'shift_id' => $shift->id,
        ]);
    }

    /**
     * Close current shift
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'actual_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $shift = CashShift::where('user_id', $user->id)->where('status', 'open')->first();

        if (!$shift && $user->role !== 'cashier') {
            $shift = CashShift::where('branch_id', $user->branch_id ?? 1)->where('status', 'open')->first();
        }

        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'لا توجد وردية مفتوحة لإغلاقها.'], 422);
        }

        $cashSales = Sale::where('cash_shift_id', $shift->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('total');

        $expectedCash = floatval($shift->opening_balance) + floatval($cashSales);
        $actualCash = floatval($request->input('actual_balance'));
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'closing_time' => now(),
            'expected_balance' => $expectedCash,
            'actual_balance' => $actualCash,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => $request->input('notes') ? trim(($shift->notes ? $shift->notes . ' | ' : '') . 'إغلاق: ' . $request->input('notes')) : $shift->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم قفل الوردية بنجاح.',
            'shift_id' => $shift->id,
            'summary' => [
                'opening_balance' => floatval($shift->opening_balance),
                'expected_balance' => $expectedCash,
                'actual_balance' => $actualCash,
                'difference' => $difference,
                'difference_label' => $difference == 0 ? 'متطابق' : ($difference > 0 ? "زيادة (+$difference)" : "عجز ($difference)"),
            ]
        ]);
    }

    /**
     * Hand over active shift to another user
     */
    public function handoverShift(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'actual_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $shift = CashShift::where('user_id', $user->id)->where('status', 'open')->first();

        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'لا توجد وردية مفتوحة لتسليمها.'], 422);
        }

        $targetUser = \App\Models\User::findOrFail($request->input('target_user_id'));

        $targetExisting = CashShift::where('user_id', $targetUser->id)->where('status', 'open')->first();
        if ($targetExisting) {
            return response()->json(['success' => false, 'message' => 'المستخدم المستلم لديه وردية مفتوحة بالفعل.'], 422);
        }

        $cashSales = Sale::where('cash_shift_id', $shift->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('total');

        $expectedCash = floatval($shift->opening_balance) + floatval($cashSales);
        $actualCash = floatval($request->input('actual_balance'));
        $difference = $actualCash - $expectedCash;

        DB::beginTransaction();
        try {
            // 1. Close current shift
            $shift->update([
                'closing_time' => now(),
                'expected_balance' => $expectedCash,
                'actual_balance' => $actualCash,
                'difference' => $difference,
                'status' => 'closed',
                'notes' => 'تسليم وردية إلى ' . $targetUser->name . ($request->input('notes') ? ' - ' . $request->input('notes') : ''),
            ]);

            // 2. Open new shift for target user
            $newShift = CashShift::create([
                'user_id' => $targetUser->id,
                'branch_id' => $shift->branch_id,
                'opening_time' => now(),
                'opening_balance' => $actualCash,
                'expected_balance' => $actualCash,
                'status' => 'open',
                'notes' => 'مستلمة من الوردية السابقة (' . $user->name . ')',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسليم الوردية بنجاح إلى: ' . $targetUser->name,
                'old_shift_id' => $shift->id,
                'new_shift_id' => $newShift->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تسليم الوردية: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Printable shift closure report
     */
    public function printShiftSummary(CashShift $shift)
    {
        $shift->load(['user', 'branch']);
        $salesQuery = Sale::where('cash_shift_id', $shift->id)->where('status', 'completed');
        $salesCount = (clone $salesQuery)->count();
        $cashSales = (clone $salesQuery)->where('payment_method', 'cash')->sum('total');
        $cardSales = (clone $salesQuery)->whereIn('payment_method', ['card', 'wallet', 'split'])->sum('total');
        $creditSales = (clone $salesQuery)->where('payment_method', 'credit')->sum('total');
        $totalSales = (clone $salesQuery)->sum('total');

        return view('pos.shift_print', compact('shift', 'salesCount', 'cashSales', 'cardSales', 'creditSales', 'totalSales'));
    }
}
