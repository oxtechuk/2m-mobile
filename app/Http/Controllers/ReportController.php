<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MaintenanceRequest;
use App\Models\Expense;
use App\Models\Wallet;
use App\Models\CashShift;
use App\Models\MoneyTransfer;
use App\Models\Branch;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $branchId = selected_branch_id();
        $period = $request->input('period', 'this_month');
        
        // Date Filtering Logic
        $fromDate = null;
        $toDate = null;

        switch ($period) {
            case 'today':
                $fromDate = Carbon::today();
                $toDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $fromDate = Carbon::yesterday();
                $toDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $fromDate = Carbon::now()->startOfWeek();
                $toDate = Carbon::now()->endOfWeek();
                break;
            case 'last_month':
                $fromDate = Carbon::now()->subMonth()->startOfMonth();
                $toDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $fromDate = $request->filled('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : Carbon::now()->startOfMonth();
                $toDate = $request->filled('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : Carbon::now()->endOfDay();
                break;
            case 'this_month':
            default:
                $period = 'this_month';
                $fromDate = Carbon::now()->startOfMonth();
                $toDate = Carbon::now()->endOfMonth();
                break;
        }

        // Base Queries scoped by Branch & Date Range
        $saleQuery = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $expenseQuery = Expense::whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()]);

        $maintQuery = MaintenanceRequest::whereBetween('created_at', [$fromDate, $toDate]);

        $shiftQuery = CashShift::whereBetween('created_at', [$fromDate, $toDate]);

        $transferQuery = MoneyTransfer::whereBetween('created_at', [$fromDate, $toDate]);

        if ($branchId !== 'all') {
            $saleQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
            $maintQuery->where('branch_id', $branchId);
            $shiftQuery->where('branch_id', $branchId);
            $transferQuery->whereHas('fromWallet', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        // 1. Sales & Revenue Stats
        $totalSales = (clone $saleQuery)->sum('total');
        $salesCount = (clone $saleQuery)->count();
        $averageTicket = $salesCount > 0 ? $totalSales / $salesCount : 0;
        $cashSales = (clone $saleQuery)->where('payment_method', 'cash')->sum('total');
        $otherSales = (clone $saleQuery)->where('payment_method', '!=', 'cash')->sum('total');

        // COGS (Cost of Goods Sold) calculation for Sales
        $saleIds = (clone $saleQuery)->pluck('id');
        $cogs = SaleItem::whereIn('sale_id', $saleIds)
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('sale_items.quantity * products.cost_price'));

        // 2. Expenses & Losses Stats
        $totalExpenses = (clone $expenseQuery)->sum('amount');
        $expensesByCategory = (clone $expenseQuery)
            ->select('category', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        // 3. Maintenance Stats & Revenues
        $totalTickets = (clone $maintQuery)->count();
        $completedTickets = (clone $maintQuery)->where('status', 'completed')->count();
        $deliveredTickets = (clone $maintQuery)->where('status', 'delivered')->count();
        $inProgressTickets = (clone $maintQuery)->where('status', 'in_progress')->count();
        $maintenanceRevenues = (clone $maintQuery)->where('status', 'delivered')->sum('final_cost');

        // 4. Shifts & Vault Stats
        $shiftsCount = (clone $shiftQuery)->count();
        $totalShiftDifferences = (clone $shiftQuery)->sum('difference');

        // 5. Money Transfers Stats
        $transfersCount = (clone $transferQuery)->count();
        $totalTransferredAmount = (clone $transferQuery)->where('status', 'approved')->sum('amount');

        // 6. Net Profit / Loss Calculation
        // Net Profit = (Sales Revenue + Maintenance Revenue) - (COGS + Expenses)
        $grossRevenue = $totalSales + $maintenanceRevenues;
        $totalCosts = $cogs + $totalExpenses;
        $netProfit = $grossRevenue - $totalCosts;
        $profitMargin = $grossRevenue > 0 ? ($netProfit / $grossRevenue) * 100 : 0;

        // 7. Top Selling Products
        $topProducts = SaleItem::whereIn('sale_id', $saleIds)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total) as total_amount'))
            ->with('product.category')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        $branches = Branch::all();

        return view('reports.sales', compact(
            'period',
            'fromDate',
            'toDate',
            'branchId',
            'branches',
            'totalSales',
            'salesCount',
            'averageTicket',
            'cashSales',
            'otherSales',
            'cogs',
            'totalExpenses',
            'expensesByCategory',
            'totalTickets',
            'completedTickets',
            'deliveredTickets',
            'inProgressTickets',
            'maintenanceRevenues',
            'shiftsCount',
            'totalShiftDifferences',
            'transfersCount',
            'totalTransferredAmount',
            'grossRevenue',
            'totalCosts',
            'netProfit',
            'profitMargin',
            'topProducts'
        ));
    }

    public function maintenance()
    {
        return redirect()->route('reports.sales', ['tab' => 'maintenance']);
    }

    public function inventory()
    {
        return redirect()->route('reports.sales', ['tab' => 'inventory']);
    }

    public function financial()
    {
        return redirect()->route('reports.sales', ['tab' => 'pnl']);
    }

    public function employees()
    {
        return redirect()->route('reports.sales', ['tab' => 'employees']);
    }

    public function export($type)
    {
        flash('سيتم تصدير التقرير المختار وقريباً سيكون متاحاً للتنزيل المباشر.')->info();
        return back();
    }
}
