<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\ProductSerial;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'branch', 'items.product'])
            ->whereIn('status', ['cancelled', 'partially_refunded', 'completed']);

        $branchId = selected_branch_id();
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->latest()->paginate(15);

        return view('returns.index', compact('sales'));
    }

    public function create(Request $request)
    {
        $sale = null;
        $matchingSales = collect();
        $search = trim($request->input('search', $request->input('invoice_number', '')));

        if (!empty($search)) {
            $query = Sale::with(['customer', 'user', 'branch', 'items.product', 'items.serials'])
                ->where('status', '!=', 'cancelled');

            $branchId = selected_branch_id();
            if ($branchId !== 'all') {
                $query->where('branch_id', $branchId);
            }

            $results = $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('barcode', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.serials', function ($sq) use ($search) {
                      $sq->where('serial_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            })->latest()->get();

            if ($results->count() === 1) {
                $sale = $results->first();
            } else {
                $matchingSales = $results;
            }
        } elseif ($request->filled('sale_id')) {
            $sale = Sale::with(['customer', 'user', 'branch', 'items.product', 'items.serials'])
                ->find($request->input('sale_id'));
        }

        return view('returns.create', compact('sale', 'matchingSales', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.qty_return' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $sale = Sale::with(['items.product', 'branch'])->findOrFail($request->input('sale_id'));

        if ($sale->status === 'cancelled') {
            flash('هذه الفاتورة ملغاة مسبقاً بالكامل.')->warning();
            return redirect()->route('returns.index');
        }

        $refundTotal = 0;
        $hasReturnedItems = false;

        DB::beginTransaction();
        try {
            foreach ($request->input('items') as $itemData) {
                $qtyReturn = floatval($itemData['qty_return']);
                if ($qtyReturn <= 0) continue;

                $saleItem = SaleItem::where('sale_id', $sale->id)->find($itemData['sale_item_id']);
                if (!$saleItem) continue;

                // Ensure return quantity doesn't exceed original sale item quantity
                $qtyToReturn = min($qtyReturn, $saleItem->quantity);
                $itemRefund = $qtyToReturn * $saleItem->unit_price;
                $refundTotal += $itemRefund;
                $hasReturnedItems = true;

                // 1. Increment product inventory in branch
                $inv = Inventory::where('branch_id', $sale->branch_id)
                    ->where('product_id', $saleItem->product_id)
                    ->first();

                if ($inv) {
                    $inv->increment('quantity', $qtyToReturn);
                }

                // 2. Free up serial / IMEI numbers
                ProductSerial::where('sale_item_id', $saleItem->id)->update([
                    'status' => 'available',
                    'sale_item_id' => null
                ]);
            }

            if (!$hasReturnedItems || $refundTotal <= 0) {
                DB::rollBack();
                flash('يرجى تحديد كمية صنف واحد على الأقل للإرجاع.')->warning();
                return redirect()->back();
            }

            // 3. Deduct refund total from branch wallet
            $wallet = Wallet::where('branch_id', $sale->branch_id)->where('is_active', true)->first();
            if ($wallet) {
                $wallet->decrement('balance', $refundTotal);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => $refundTotal,
                    'balance_after' => $wallet->balance,
                    'category' => 'refund',
                    'description' => 'مرتجع مبيعات للفاتورة رقم: ' . $sale->invoice_number . ($request->reason ? ' - السبب: ' . $request->reason : ''),
                    'reference_id' => $sale->id,
                    'reference_type' => 'ProductReturn',
                    'performed_by' => Auth::id(),
                ]);
            }

            // 4. Update Sale Status
            $totalReturnedSoFar = $refundTotal;
            if ($totalReturnedSoFar >= $sale->total) {
                $sale->update(['status' => 'cancelled']);
            } else {
                $sale->update(['status' => 'partially_refunded']);
            }

            DB::commit();

            flash('تم تسجيل مرتجع المبيعات بنجاح وتحديث رصيد المخزن والخزينة.')->success();
            return redirect()->route('returns.index');

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء معالجة المرتجع: ' . $e->getMessage())->error();
            return redirect()->back();
        }
    }
}
