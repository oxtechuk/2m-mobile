<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payroll;
use App\Models\EmployeeAdjustment;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class ProcessMonthlyPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:process-monthly {--month=} {--year=} {--branch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and calculate monthly payroll for all active employees across all branches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = (int) ($this->option('year') ?: date('Y'));
        $month = (int) ($this->option('month') ?: date('n'));
        $branchId = $this->option('branch');

        $this->info("بدء معالجة واحتساب مسير الرواتب لشهر {$month}/{$year}...");

        $query = User::where('is_active', true);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $employees = $query->get();
        $processed = 0;

        DB::transaction(function () use ($employees, $year, $month, &$processed) {
            foreach ($employees as $emp) {
                $payroll = Payroll::firstOrNew([
                    'user_id' => $emp->id,
                    'year' => $year,
                    'month' => $month,
                ]);

                // Skip if already paid
                if ($payroll->exists && $payroll->status === 'paid') {
                    continue;
                }

                $basicSalary = (float) ($emp->salary ?? 0);

                // Adjustments
                $adjustments = EmployeeAdjustment::where('user_id', $emp->id)
                    ->where('status', 'pending')
                    ->whereYear('date', '<=', $year)
                    ->whereMonth('date', '<=', $month)
                    ->get();

                $allowances = (float) $adjustments->where('type', 'allowance')->sum('amount');
                $bonuses = (float) $adjustments->where('type', 'bonus')->sum('amount');
                $deductions = (float) $adjustments->where('type', 'deduction')->sum('amount');
                $advances = (float) $adjustments->where('type', 'advance')->sum('amount');

                // Commissions
                $commissions = (float) Commission::where('user_id', $emp->id)
                    ->where('status', 'approved')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('amount');

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

                $processed++;
            }
        });

        $this->info("✅ تم معالجة وتوليد كشف رواتب ({$processed}) موظف بنجاح.");
        return Command::SUCCESS;
    }
}
