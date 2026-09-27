<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Commission;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesRepController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determine Selected Month and Year
        $selectedMonth = $request->input('month', now()->format('Y-m')); // format: YYYY-MM
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();

        $selectedBranchId = $request->input('branch_id');

        // 2. Fetch Users (Reps / Cashiers / Admins who conduct sales)
        $usersQuery = User::query();

        if ($selectedBranchId && $selectedBranchId !== 'all') {
            $usersQuery->where('branch_id', $selectedBranchId);
        }

        $users = $usersQuery->where('is_active', true)->with('branch')->get();

        // 3. Compute Sales Statistics per Representative for the Selected Month
        $repsData = collect();

        foreach ($users as $user) {
            $salesQuery = Sale::where('cashier_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            if ($selectedBranchId && $selectedBranchId !== 'all') {
                $salesQuery->where('branch_id', $selectedBranchId);
            }

            $totalSalesAmount = (float) $salesQuery->sum('total');
            $salesCount = (int) $salesQuery->count();
            $avgOrderValue = $salesCount > 0 ? ($totalSalesAmount / $salesCount) : 0;

            // Calculate commission: check recorded commissions table or compute using commission_rate
            $recordedCommissions = (float) Commission::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $commissionRate = (float) ($user->commission_rate ?? 0);
            $calculatedCommission = $totalSalesAmount * ($commissionRate / 100);

            $earnedCommission = max($recordedCommissions, $calculatedCommission);

            // Include users who either have sales or are active reps
            if ($salesCount > 0 || $totalSalesAmount > 0 || $user->hasRole('admin') || $user->role === 'cashier' || $user->role === 'sales_rep') {
                $repsData->push((object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'branch_name' => $user->branch->name ?? 'جميع الفروع',
                    'commission_rate' => $commissionRate,
                    'total_sales' => $totalSalesAmount,
                    'sales_count' => $salesCount,
                    'avg_order_value' => $avgOrderValue,
                    'earned_commission' => $earnedCommission,
                ]);
            }
        }

        // 4. Sort Reps by Total Sales Amount Descending
        $sortedReps = $repsData->sortByDesc('total_sales')->values();

        // Top 3 Leaderboard Reps
        $top1 = $sortedReps->get(0);
        $top2 = $sortedReps->get(1);
        $top3 = $sortedReps->get(2);

        // Overall Team Totals
        $teamStats = [
            'total_sales' => $sortedReps->sum('total_sales'),
            'total_invoices' => $sortedReps->sum('sales_count'),
            'total_commissions' => $sortedReps->sum('earned_commission'),
            'top_seller' => $top1->name ?? 'لا يوجد',
        ];

        $branches = Branch::where('is_active', true)->get();

        return view('sales_reps.index', compact(
            'sortedReps',
            'top1',
            'top2',
            'top3',
            'teamStats',
            'selectedMonth',
            'selectedBranchId',
            'branches'
        ));
    }

    public function show(User $rep, Request $request)
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();

        // Fetch Rep Sales in the Month
        $sales = Sale::where('cashier_id', $rep->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->with(['customer', 'branch'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSalesAmount = (float) Sale::where('cashier_id', $rep->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $salesCount = (int) Sale::where('cashier_id', $rep->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $commissionRate = (float) ($rep->commission_rate ?? 0);
        $earnedCommission = $totalSalesAmount * ($commissionRate / 100);

        // Daily trend data for chart
        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as daily_total'),
                DB::raw('COUNT(*) as daily_count')
            )
            ->where('cashier_id', $rep->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('sales_reps.show', compact(
            'rep',
            'sales',
            'totalSalesAmount',
            'salesCount',
            'commissionRate',
            'earnedCommission',
            'dailySales',
            'selectedMonth'
        ));
    }

    public function updateCommissionRate(Request $request, User $rep)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $rep->update([
            'commission_rate' => floatval($request->input('commission_rate')),
        ]);

        flash("تم تحديث نسبة عمولة المندوب ({$rep->name}) إلى " . number_format($request->input('commission_rate'), 2) . "% بنجاح.")->success();
        return back();
    }
}
