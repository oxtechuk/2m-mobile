<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use App\Models\Wallet;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\EmployeeAdjustment;
use App\Models\Commission;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Display the monthly payroll sheet and overview.
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $branchId = $request->input('branch_id');

        $branches = Branch::all();
        $wallets = Wallet::all();

        $query = Payroll::with(['user.branch', 'branch', 'wallet', 'approver', 'payer'])
            ->where('year', $year)
            ->where('month', $month);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $payrolls = $query->get();

        // Statistics Summary
        $stats = [
            'total_basic' => $payrolls->sum('basic_salary'),
            'total_allowances' => $payrolls->sum('total_allowances'),
            'total_bonuses' => $payrolls->sum('total_bonuses'),
            'total_commissions' => $payrolls->sum('total_commissions'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'total_advances' => $payrolls->sum('total_advances'),
            'total_net' => $payrolls->sum('net_salary'),
            'paid_count' => $payrolls->where('status', 'paid')->count(),
            'approved_count' => $payrolls->where('status', 'approved')->count(),
            'draft_count' => $payrolls->where('status', 'draft')->count(),
            'total_count' => $payrolls->count(),
        ];

        return view('payroll.index', compact('payrolls', 'stats', 'year', 'month', 'branchId', 'branches', 'wallets'));
    }

    /**
     * Generate or recalculate draft payroll for all active employees for a given month/year.
     */
    public function generate(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));
        $branchId = $request->input('branch_id');

        $query = User::where('is_active', true);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $employees = $query->get();
        $generatedCount = 0;

        DB::transaction(function () use ($employees, $year, $month, &$generatedCount) {
            foreach ($employees as $emp) {
                // Find existing draft payroll or create a new one
                $payroll = Payroll::firstOrNew([
                    'user_id' => $emp->id,
                    'year' => $year,
                    'month' => $month,
                ]);

                // Do not recalculate if already paid
                if ($payroll->exists && $payroll->status === 'paid') {
                    continue;
                }

                $basicSalary = (float) ($emp->salary ?? 0);

                // 1. Calculate pending adjustments for this user up to this month
                $adjustments = EmployeeAdjustment::where('user_id', $emp->id)
                    ->where('status', 'pending')
                    ->whereYear('date', '<=', $year)
                    ->whereMonth('date', '<=', $month)
                    ->get();

                $allowances = (float) $adjustments->where('type', 'allowance')->sum('amount');
                $bonuses = (float) $adjustments->where('type', 'bonus')->sum('amount');
                $deductions = (float) $adjustments->where('type', 'deduction')->sum('amount');
                $advances = (float) $adjustments->where('type', 'advance')->sum('amount');

                // 2. Calculate approved commissions for this month
                $commissions = (float) Commission::where('user_id', $emp->id)
                    ->where('status', 'approved')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('amount');

                // 3. Calculate Net Salary
                $netSalary = max(0, $basicSalary + $allowances + $bonuses + $commissions - $deductions - $advances);

                $payroll->branch_id = $emp->branch_id;
                $payroll->basic_salary = $basicSalary;
                $payroll->total_allowances = $allowances;
                $payroll->total_bonuses = $bonuses;
                $payroll->total_commissions = $commissions;
                $payroll->total_deductions = $deductions;
                $payroll->total_advances = $advances;
                $payroll->net_salary = $netSalary;
                $payroll->status = $payroll->status === 'approved' ? 'approved' : 'draft';
                $payroll->save();

                $generatedCount++;
            }
        });

        flash("تم احتساب وتوليد مسير رواتب ({$generatedCount}) موظف بنجاح لشهر {$month}/{$year}.")->success();

        return redirect()->route('payroll.index', ['year' => $year, 'month' => $month, 'branch_id' => $branchId]);
    }

    /**
     * Approve a single draft payroll.
     */
    public function approve(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            flash('تم صرف هذا الراتب بالفعل مسبقاً.')->info();
            return back();
        }

        $payroll->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        flash('تم اعتماد الراتب بنجاح وجاهز للصرف المالي.')->success();
        return back();
    }

    /**
     * Disburse / Pay a single payroll and deduct from wallet.
     */
    public function pay(Request $request, Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            flash('تم صرف هذا الراتب مسبقاً.')->info();
            return back();
        }

        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $wallet = Wallet::findOrFail($request->input('wallet_id'));

        if ($wallet->balance < $payroll->net_salary) {
            flash('رصيد الخزينة / المحفظة غير كافٍ لصرف هذا الراتب. الرصيد الحالي: ' . number_format($wallet->balance, 2) . ' ج.م')->error();
            return back();
        }

        DB::transaction(function () use ($payroll, $wallet, $request) {
            // 1. Create an Expense record for the salary payout
            $expense = Expense::create([
                'branch_id' => $payroll->branch_id ?? $wallet->branch_id ?? 1,
                'category' => 'رواتب وأجور',
                'description' => "صرف راتب الموظف: {$payroll->user->name} لشهر {$payroll->month}/{$payroll->year}",
                'amount' => $payroll->net_salary,
                'date' => now()->toDateString(),
                'wallet_id' => $wallet->id,
                'recorded_by' => auth()->id(),
            ]);

            // 2. Deduct from wallet & record transaction
            $oldBalance = $wallet->balance;
            $wallet->balance -= $payroll->net_salary;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'expense',
                'amount' => $payroll->net_salary,
                'balance_before' => $oldBalance,
                'balance_after' => $wallet->balance,
                'reference_type' => 'App\Models\Payroll',
                'reference_id' => $payroll->id,
                'description' => "صرف راتب شهر {$payroll->month}/{$payroll->year} للموظف {$payroll->user->name}",
                'performed_by' => auth()->id(),
            ]);

            // 3. Mark pending adjustments as settled
            EmployeeAdjustment::where('user_id', $payroll->user_id)
                ->where('status', 'pending')
                ->whereYear('date', '<=', $payroll->year)
                ->whereMonth('date', '<=', $payroll->month)
                ->update([
                    'status' => 'settled',
                    'payroll_id' => $payroll->id,
                ]);

            // 4. Mark commissions as paid
            Commission::where('user_id', $payroll->user_id)
                ->where('status', 'approved')
                ->whereYear('created_at', $payroll->year)
                ->whereMonth('created_at', $payroll->month)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

            // 5. Update Payroll record
            $payroll->update([
                'status' => 'paid',
                'wallet_id' => $wallet->id,
                'expense_id' => $expense->id,
                'paid_at' => now(),
                'paid_by' => auth()->id(),
                'notes' => $request->input('notes', $payroll->notes),
            ]);
        });

        flash("تم صرف راتب الموظف ({$payroll->user->name}) بمبلغ " . number_format($payroll->net_salary, 2) . " ج.م وخصمه من الخزينة بنجاح.")->success();

        return back();
    }

    /**
     * Bulk pay multiple approved payrolls in 1 transaction.
     */
    public function bulkPay(Request $request)
    {
        $request->validate([
            'payroll_ids' => 'required|array',
            'payroll_ids.*' => 'exists:payrolls,id',
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        $wallet = Wallet::findOrFail($request->input('wallet_id'));
        $payrolls = Payroll::whereIn('id', $request->input('payroll_ids'))
            ->where('status', '!=', 'paid')
            ->get();

        if ($payrolls->isEmpty()) {
            flash('لم يتم تحديد أي رواتب صالحة للصرف.')->warning();
            return back();
        }

        $totalAmount = $payrolls->sum('net_salary');

        if ($wallet->balance < $totalAmount) {
            flash('رصيد الخزينة غير كافٍ لصرف إجمالي الرواتب المحددة (' . number_format($totalAmount, 2) . ' ج.م). الرصيد المتاح: ' . number_format($wallet->balance, 2) . ' ج.م')->error();
            return back();
        }

        DB::transaction(function () use ($payrolls, $wallet, $totalAmount) {
            foreach ($payrolls as $payroll) {
                // Expense record
                $expense = Expense::create([
                    'branch_id' => $payroll->branch_id ?? $wallet->branch_id ?? 1,
                    'category' => 'رواتب وأجور',
                    'description' => "صرف راتب الموظف: {$payroll->user->name} لشهر {$payroll->month}/{$payroll->year}",
                    'amount' => $payroll->net_salary,
                    'date' => now()->toDateString(),
                    'wallet_id' => $wallet->id,
                    'recorded_by' => auth()->id(),
                ]);

                // Settle adjustments
                EmployeeAdjustment::where('user_id', $payroll->user_id)
                    ->where('status', 'pending')
                    ->whereYear('date', '<=', $payroll->year)
                    ->whereMonth('date', '<=', $payroll->month)
                    ->update([
                        'status' => 'settled',
                        'payroll_id' => $payroll->id,
                    ]);

                // Settle commissions
                Commission::where('user_id', $payroll->user_id)
                    ->where('status', 'approved')
                    ->whereYear('created_at', $payroll->year)
                    ->whereMonth('created_at', $payroll->month)
                    ->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                $payroll->update([
                    'status' => 'paid',
                    'wallet_id' => $wallet->id,
                    'expense_id' => $expense->id,
                    'paid_at' => now(),
                    'paid_by' => auth()->id(),
                ]);
            }

            // Deduct total from wallet once
            $oldBalance = $wallet->balance;
            $wallet->balance -= $totalAmount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'expense',
                'amount' => $totalAmount,
                'balance_before' => $oldBalance,
                'balance_after' => $wallet->balance,
                'reference_type' => 'App\Models\PayrollBulk',
                'description' => "صرف جماعي لرواتب " . $payrolls->count() . " موظفين",
                'performed_by' => auth()->id(),
            ]);
        });

        flash("تم صرف رواتب " . $payrolls->count() . " موظف بنجاح بإجمالي " . number_format($totalAmount, 2) . " ج.م.")->success();

        return back();
    }

    /**
     * Display printable payslip view for a specific payroll record.
     */
    public function payslip(Payroll $payroll)
    {
        $payroll->load(['user.branch', 'branch', 'wallet', 'payer', 'adjustments']);
        return view('payroll.payslip', compact('payroll'));
    }

    /**
     * List all employee adjustments (advances, deductions, bonuses).
     */
    public function adjustments(Request $request)
    {
        $branches = Branch::all();
        $employees = User::where('is_active', true)->get();
        $wallets = Wallet::all();

        $type = $request->input('type');
        $status = $request->input('status');
        $userId = $request->input('user_id');

        $query = EmployeeAdjustment::with(['user.branch', 'branch', 'wallet', 'creator'])
            ->latest('date');

        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $adjustments = $query->paginate(25)->withQueryString();

        // Summary Stats
        $pendingAdvances = EmployeeAdjustment::where('type', 'advance')->where('status', 'pending')->sum('amount');
        $pendingDeductions = EmployeeAdjustment::where('type', 'deduction')->where('status', 'pending')->sum('amount');
        $pendingBonuses = EmployeeAdjustment::where('type', 'bonus')->where('status', 'pending')->sum('amount');

        return view('payroll.adjustments', compact('adjustments', 'employees', 'branches', 'wallets', 'pendingAdvances', 'pendingDeductions', 'pendingBonuses', 'type', 'status', 'userId'));
    }

    /**
     * Store a new employee adjustment (Advance, Deduction, Bonus, Allowance).
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:advance,deduction,bonus,allowance',
            'amount' => 'required|numeric|min:0.5',
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
            'wallet_id' => 'nullable|exists:wallets,id', // Required if advance paid from wallet
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $amount = (float) $request->input('amount');
        $type = $request->input('type');
        $walletId = $request->input('wallet_id');

        // If it's an advance and a wallet is selected, deduct from wallet right now
        if ($type === 'advance' && $walletId) {
            $wallet = Wallet::findOrFail($walletId);
            if ($wallet->balance < $amount) {
                flash('رصيد الخزينة / المحفظة المحددة غير كافٍ لصرف هذه السلفة.')->error();
                return back();
            }

            $oldBalance = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'expense',
                'amount' => $amount,
                'balance_before' => $oldBalance,
                'balance_after' => $wallet->balance,
                'reference_type' => 'App\Models\EmployeeAdvance',
                'description' => "صرف سلفة نقدية للموظف: {$user->name} - " . $request->input('reason'),
                'performed_by' => auth()->id(),
            ]);
        }

        EmployeeAdjustment::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'type' => $type,
            'amount' => $amount,
            'date' => $request->input('date'),
            'reason' => $request->input('reason'),
            'status' => 'pending',
            'wallet_id' => $walletId,
            'created_by' => auth()->id(),
        ]);

        flash('تم تسجيل حركة الموظف بنجاح وسيتم تسويتها تلقائياً في مسير الراتب القادم.')->success();

        return redirect()->route('payroll.adjustments');
    }

    /**
     * Delete or cancel an adjustment if still pending.
     */
    public function destroyAdjustment(EmployeeAdjustment $adjustment)
    {
        if ($adjustment->status === 'settled') {
            flash('لا يمكن حذف هذه الحركة لأنها تم تسويتها وخصمها من الراتب بالفعل.')->error();
            return back();
        }

        // If it was an advance paid from a wallet, refund to wallet
        if ($adjustment->type === 'advance' && $adjustment->wallet_id) {
            $wallet = Wallet::find($adjustment->wallet_id);
            if ($wallet) {
                $oldBalance = $wallet->balance;
                $wallet->balance += $adjustment->amount;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'income',
                    'amount' => $adjustment->amount,
                    'balance_before' => $oldBalance,
                    'balance_after' => $wallet->balance,
                    'reference_type' => 'App\Models\EmployeeAdvanceRefund',
                    'description' => "استرداد سلفة ملغاة للموظف: {$adjustment->user->name}",
                    'performed_by' => auth()->id(),
                ]);
            }
        }

        $adjustment->delete();

        flash('تم حذف وإلغاء حركة الموظف بنجاح.')->warning();
        return back();
    }
}
