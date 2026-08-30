<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\ProductSerial;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'branch']);
        $branchId = selected_branch_id();
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        // Cashier view constraint: cashiers only view their own sales in their branch
        if (Auth::user()->role === 'cashier') {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
        }

        $sales = $query->latest()->get();

        return view('sales.index', compact('sales'));
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product'])->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    public function void($id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status === 'cancelled') {
            flash('عفواً، هذه الفاتورة ملغاة بالفعل.')->error();
            return redirect()->route('sales.index');
        }

        DB::beginTransaction();
        try {
            // 1. Restore product inventory and serial statuses
            foreach ($sale->items as $item) {
                // Restore Stock quantity
                $inv = Inventory::where('branch_id', $sale->branch_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($inv) {
                    $inv->increment('quantity', $item->quantity);
                }

                // Restore Serial/IMEI status to available
                ProductSerial::where('sale_item_id', $item->id)->update([
                    'status' => 'available',
                    'sale_item_id' => null
                ]);
            }

            // 2. Subtract invoice amount from branch wallet
            $wallet = Wallet::where('branch_id', $sale->branch_id)->where('is_active', true)->first();
            if ($wallet) {
                $wallet->decrement('balance', $sale->total);

                // Add wallet withdrawal log
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => $sale->total,
                    'balance_after' => $wallet->balance,
                    'category' => 'refund',
                    'description' => 'سحب قيمة إلغاء الفاتورة رقم: ' . $sale->invoice_number,
                    'reference_id' => $sale->id,
                    'reference_type' => 'SaleVoid',
                    'performed_by' => Auth::id(),
                ]);
            }

            // 3. Deduct loyalty points from customer
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $pointsToDeduct = floor($sale->total / 100);
                    $customer->decrement('loyalty_points', min($customer->loyalty_points, $pointsToDeduct));
                    $customer->decrement('total_purchases', $sale->total);
                }
            }

            // 4. Update sale invoice status to cancelled
            $sale->update(['status' => 'cancelled']);

            DB::commit();
            flash('تم إلغاء الفاتورة وإرجاع الكميات للمخزن واسترداد المبالغ بنجاح.')->warning();

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء إلغاء الفاتورة: ' . $e->getMessage())->error();
        }

        return redirect()->route('sales.index');
    }

    public function invoice($id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product'])->findOrFail($id);
        return view('sales.invoice', compact('sale'));
    }

    /**
     * Direct Zero-Waste Hardware Printing for Xprinter XP-370BM
     */
    public function directPrint(Request $request, $id)
    {
        $sale = Sale::with(['customer', 'user', 'branch', 'items.product'])->findOrFail($id);
        $printerName = $request->input('printer_name', 'Xprinter XP-370BM');

        // Build Zero-Waste ESC/POS & Text Receipt Stream
        $esc = "";
        // 1. Initialize Printer
        $esc .= "\x1B\x40"; 
        
        // 2. Center Align & Double Size Store Header
        $esc .= "\x1B\x61\x01"; // Center
        $esc .= "\x1D\x21\x11"; // Double Height & Width
        $esc .= setting('store_name', '2M Mobile') . "\n";
        $esc .= "\x1D\x21\x00"; // Normal Size
        $esc .= "مبيعات وصيانة الهواتف الذكية\n";
        $branchName = $sale->branch->name ?? 'الفرع الرئيسي';
        $branchPhone = $sale->branch->phone ?? setting('store_phone', '01011111111');
        $esc .= $branchName . " - هاتف: " . $branchPhone . "\n";
        $esc .= "------------------------------------------\n";

        // 3. Meta Information (Left Align)
        $esc .= "\x1B\x61\x00"; // Left
        $esc .= "رقم الفاتورة: #" . $sale->invoice_number . "\n";
        $esc .= "التاريخ: " . $sale->created_at->format('Y-m-d h:i A') . "\n";
        $esc .= "الكاشير: " . ($sale->user->name ?? 'الكاشير') . "\n";
        $esc .= "العميل: " . ($sale->customer->name ?? 'عميل نقدي عام') . "\n";
        if ($sale->customer && $sale->customer->phone) {
            $esc .= "هاتف العميل: " . $sale->customer->phone . "\n";
        }
        $esc .= "------------------------------------------\n";

        // 4. Table Header & Items
        $esc .= sprintf("%-20s %4s %8s %8s\n", "الصنف", "الكمية", "السعر", "الإجمالي");
        $esc .= "------------------------------------------\n";

        foreach ($sale->items as $item) {
            $name = mb_substr($item->product->name ?? 'منتج', 0, 20);
            $qty = $item->quantity;
            $price = number_format($item->unit_price, 2);
            $total = number_format($item->total_price, 2);
            $esc .= sprintf("%-20s %4d %8s %8s\n", $name, $qty, $price, $total);
            
            // IMEI / Serial Number if exists
            $serials = \App\Models\ProductSerial::where('sale_item_id', $item->id)->pluck('serial_number');
            if ($serials->count() > 0) {
                $esc .= "  SN: " . $serials->implode(', ') . "\n";
            }
        }
        $esc .= "------------------------------------------\n";

        // 5. Financial Summary
        $currency = setting('default_currency', 'ج.م');
        $esc .= sprintf("%-24s %16s %s\n", "المجموع الفرعي:", number_format($sale->subtotal, 2), $currency);
        if ($sale->tax > 0) {
            $esc .= sprintf("%-24s %16s %s\n", "ضريبة القيمة المضافة:", number_format($sale->tax, 2), $currency);
        }
        if ($sale->discount > 0) {
            $esc .= sprintf("%-24s -%15s %s\n", "الخصم الممنوح:", number_format($sale->discount, 2), $currency);
        }
        
        $esc .= "==========================================\n";
        $esc .= "\x1D\x21\x01"; // Double Height
        $esc .= sprintf("%-18s %16s %s\n", "الإجمالي النهائي:", number_format($sale->total, 2), $currency);
        $esc .= "\x1D\x21\x00"; // Normal Size
        $esc .= "==========================================\n";

        // 6. Footer (Center)
        $esc .= "\x1B\x61\x01"; // Center
        $esc .= "شكراً لاختياركم " . setting('store_name', '2M Mobile') . "!\n";
        $esc .= "البضاعة ترد وتستبدل خلال 14 يوماً بالفاتورة\n";
        $esc .= "برمجة وتطوير شركة Ox Tech | oxtech.uk\n";
        $esc .= "\n\n"; // Minimum feed to reach cutter

        // 7. Full/Partial Cut
        $esc .= "\x1D\x56\x42\x00"; // GS V 66 0 (Cut immediately)

        // Check if running on Windows OS with shell_exec enabled
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $psScript = storage_path('app/print_raw.ps1');
        $canExec = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        if ($isWindows && $canExec && file_exists($psScript)) {
            $tempFile = storage_path('app/receipt_' . $sale->id . '_' . time() . '.prn');
            file_put_contents($tempFile, $esc);

            $command = 'powershell -NoProfile -ExecutionPolicy Bypass -File "' . $psScript . '" -PrinterName "' . $printerName . '" -RawFile "' . $tempFile . '"';
            $output = @shell_exec($command);
            @unlink($tempFile);

            $res = json_decode($output, true);
            if ($res && isset($res['success']) && $res['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تمت طباعة الفاتورة حرارياً بنجاح بدون أي هدر في الورق!'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'fallback_browser' => true,
            'esc_data' => base64_encode($esc),
            'message' => 'الخادم يعمل بنظام Cloud/Linux. سيتم استخدام نافذة الطباعة الحرارية من المتصفح.'
        ], 200);
    }
}
