<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'branch', 'creator']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $purchases = $query->latest()->paginate(15)->withQueryString();

        $suppliers = Supplier::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        $stats = [
            'total_purchases' => Purchase::count(),
            'total_amount' => Purchase::sum('total_amount'),
            'total_paid' => Purchase::sum('paid_amount'),
            'total_due' => Purchase::sum('due_amount'),
        ];

        return view('purchases.index', compact('purchases', 'suppliers', 'branches', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();
        $wallets = Wallet::where('is_active', true)->get();

        return view('purchases.create', compact('suppliers', 'branches', 'products', 'wallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.serials' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'wallet_id' => 'nullable|required_if:paid_amount,>0|exists:wallets,id',
            'payment_method' => 'required|string',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($request->input('supplier_id'));
        $branchId = $request->input('branch_id');
        $paidAmount = floatval($request->input('paid_amount'));
        $walletId = $request->input('wallet_id');

        // Check wallet balance if paying upfront
        if ($paidAmount > 0 && $walletId) {
            $wallet = Wallet::findOrFail($walletId);
            if ($wallet->balance < $paidAmount) {
                flash('عفواً، رصيد الخزينة المحددة غير كافٍ لسداد قيمة الدفعة الحالية (الرصيد المتاح: ' . number_format($wallet->balance, 2) . ' ج.م).')->error();
                return back()->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->input('items') as $item) {
                $qty = intval($item['quantity']);
                $cost = floatval($item['unit_cost']);
                $subtotal = $qty * $cost;
                $totalAmount += $subtotal;

                $serialsArr = [];
                if (!empty($item['serials'])) {
                    $lines = array_map('trim', explode("\n", $item['serials']));
                    $serialsArr = array_filter($lines);
                }

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                    'serials' => $serialsArr,
                ];
            }

            $dueAmount = max(0, $totalAmount - $paidAmount);
            $paymentStatus = 'unpaid';
            if ($paidAmount >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            // Generate unique Invoice Number
            $invoiceNumber = 'PUR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // 1. Create Purchase Record
            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $supplier->id,
                'branch_id' => $branchId,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $request->input('payment_method'),
                'purchase_date' => $request->input('purchase_date'),
                'notes' => $request->input('notes'),
                'created_by' => Auth::id() ?? 1,
            ]);

            // 2. Create Purchase Items & Update Inventory
            foreach ($itemsData as $item) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'serials' => $item['serials'],
                ]);

                // Update product cost price to latest purchase cost
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->update([
                        'cost_price' => $item['unit_cost'],
                    ]);
                }

                // Update branch inventory quantity
                $inventory = Inventory::firstOrCreate(
                    [
                        'product_id' => $item['product_id'],
                        'branch_id' => $branchId,
                    ],
                    [
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                    ]
                );

                $inventory->increment('quantity', $item['quantity']);
                $inventory->update(['last_restock_at' => now()]);

                // Log Inventory Movement
                InventoryMovement::create([
                    'product_id' => $item['product_id'],
                    'branch_id' => $branchId,
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->id,
                    'notes' => "توريد مشتريات بموجب الفاتورة {$invoiceNumber} من المورد {$supplier->name}",
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Insert Serials if any
                if (!empty($item['serials'])) {
                    foreach ($item['serials'] as $serialNum) {
                        ProductSerial::create([
                            'product_id' => $item['product_id'],
                            'serial_number' => $serialNum,
                            'status' => 'available',
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            }

            // 3. Supplier Financial Account Updates
            // Add full purchase invoice amount to supplier balance
            $balanceAfterInvoice = $supplier->current_balance + $totalAmount;
            $supplier->update(['current_balance' => $balanceAfterInvoice]);

            SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'type' => 'purchase_invoice',
                'amount' => $totalAmount,
                'balance_after' => $balanceAfterInvoice,
                'reference_type' => 'Purchase',
                'reference_id' => $purchase->id,
                'notes' => "فاتورة توريد مشتريات رقم {$invoiceNumber}",
                'created_by' => Auth::id() ?? 1,
            ]);

            // Deduct paid amount if any
            if ($paidAmount > 0) {
                $finalBalance = $supplier->current_balance - $paidAmount;
                $supplier->update(['current_balance' => $finalBalance]);

                $supplierTx = SupplierTransaction::create([
                    'supplier_id' => $supplier->id,
                    'type' => 'payment_out',
                    'amount' => $paidAmount,
                    'balance_after' => $finalBalance,
                    'payment_method' => $request->input('payment_method'),
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->id,
                    'notes' => "دفعة مسددة فورية للفاتورة رقم {$invoiceNumber}",
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Deduct from Wallet
                if ($walletId) {
                    $wallet = Wallet::find($walletId);
                    if ($wallet) {
                        $wallet->decrement('balance', $paidAmount);

                        WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'type' => 'debit',
                            'amount' => $paidAmount,
                            'balance_after' => $wallet->balance,
                            'category' => 'purchase_payment',
                            'description' => "دفعة شراء توريد فاتورة {$invoiceNumber} للمورد {$supplier->name}",
                            'reference_id' => $purchase->id,
                            'reference_type' => 'Purchase',
                            'performed_by' => Auth::id() ?? 1,
                        ]);
                    }
                }
            }

            DB::commit();
            flash("تم تسجيل فاتورة الشراء والتوريد رقم ({$invoiceNumber}) وتحديث رصيد المخزون وحساب المورد بنجاح.")->success();
            return redirect()->route('purchases.show', $purchase->id);

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء حفظ فاتورة الشراء: ' . $e->getMessage())->error();
            return back()->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'branch', 'creator', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
}
