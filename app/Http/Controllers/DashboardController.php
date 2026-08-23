<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\MaintenanceRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CashShift;
use App\Models\Branch;
use App\Models\Inventory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        $currentBranch = Branch::find($branchId);

        // 1. Sales Metrics
        $todaySalesTotal = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total');

        $yesterdaySalesTotal = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', Carbon::yesterday())
            ->where('status', 'completed')
            ->sum('total');

        $todaySalesCount = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();

        // Calculate growth percentage
        if ($yesterdaySalesTotal > 0) {
            $salesGrowth = (($todaySalesTotal - $yesterdaySalesTotal) / $yesterdaySalesTotal) * 100;
        } else {
            $salesGrowth = $todaySalesTotal > 0 ? 100 : 0;
        }

        // 2. Maintenance Metrics
        $activeMaintenanceCount = MaintenanceRequest::where('branch_id', $branchId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $waitingPartsCount = MaintenanceRequest::where('branch_id', $branchId)
            ->where('status', 'waiting_parts')
            ->count();

        // 3. Customers Metrics
        $totalCustomersCount = Customer::count();
        $todayNewCustomersCount = Customer::whereDate('created_at', Carbon::today())->count();

        // 4. Cash Shift & Vault Balance
        $activeShift = CashShift::where('branch_id', $branchId)
            ->where('status', 'open')
            ->latest()
            ->first();

        if ($activeShift) {
            $shiftSalesSum = Sale::where('cash_shift_id', $activeShift->id)
                ->where('status', 'completed')
                ->sum('total');
            $vaultBalance = $activeShift->opening_balance + $shiftSalesSum;
        } else {
            $vaultBalance = $todaySalesTotal;
        }

        // 5. Low Stock Alerts for active branch
        $lowStockProducts = Product::with(['category', 'inventories' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->where('is_active', true)
            ->get()
            ->map(function($product) use ($branchId) {
                $inv = $product->inventories->firstWhere('branch_id', $branchId);
                $product->stock_quantity = $inv ? $inv->quantity : 0;
                return $product;
            })
            ->filter(function($product) {
                return $product->stock_quantity <= max(5, $product->minimum_stock ?? 0);
            })
            ->sortBy('stock_quantity')
            ->take(6);

        // 6. Recent Sales Invoices
        $recentSales = Sale::with(['customer', 'branch'])
            ->where('branch_id', $branchId)
            ->latest()
            ->take(5)
            ->get();

        // 7. Weekly Chart Data (Last 7 Days)
        $chartLabels = [];
        $chartSalesData = [];
        $chartOrdersData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $date->locale('ar')->translatedFormat('l j F');
            
            $daySalesSum = Sale::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total');

            $dayOrdersCount = Sale::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();

            $chartLabels[] = $dayLabel;
            $chartSalesData[] = (float) $daySalesSum;
            $chartOrdersData[] = (int) $dayOrdersCount;
        }

        return view('dashboard', compact(
            'currentBranch',
            'todaySalesTotal',
            'yesterdaySalesTotal',
            'todaySalesCount',
            'salesGrowth',
            'activeMaintenanceCount',
            'waitingPartsCount',
            'totalCustomersCount',
            'todayNewCustomersCount',
            'activeShift',
            'vaultBalance',
            'lowStockProducts',
            'recentSales',
            'chartLabels',
            'chartSalesData',
            'chartOrdersData'
        ));
    }

    public function stats()
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        
        return response()->json([
            'success' => true,
            'today_sales' => Sale::where('branch_id', $branchId)->whereDate('created_at', Carbon::today())->sum('total'),
            'active_maintenance' => MaintenanceRequest::where('branch_id', $branchId)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
        ]);
    }

    public function chartData($type)
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('Y-m-d');
            $chartData[] = Sale::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total');
        }

        return response()->json([
            'success' => true,
            'labels' => $chartLabels,
            'data' => $chartData
        ]);
    }

    public function switchBranch(Request $request)
    {
        $branchId = $request->input('branch_id');
        
        session(['active_branch_id' => $branchId]);
        
        flash('تم تغيير الفرع النشط بنجاح.')->success();
        
        return back();
    }
}
