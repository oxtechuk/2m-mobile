<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Add HR and payroll fields to users table if not already present
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('salary');
            }
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id', 30)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone', 20)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('users', 'salary_payment_day')) {
                $table->unsignedTinyInteger('salary_payment_day')->default(1)->after('salary');
            }
            if (!Schema::hasColumn('users', 'salary_type')) {
                $table->enum('salary_type', ['monthly', 'daily', 'commission_only'])->default('monthly')->after('salary_payment_day');
            }
        });

        // 2. Create payrolls table (Monthly payroll records)
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable();
                $table->foreignId('branch_id')->nullable();
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month'); // 1 to 12
                $table->decimal('basic_salary', 12, 2)->default(0);
                $table->decimal('total_allowances', 12, 2)->default(0);
                $table->decimal('total_bonuses', 12, 2)->default(0);
                $table->decimal('total_commissions', 12, 2)->default(0);
                $table->decimal('total_deductions', 12, 2)->default(0);
                $table->decimal('total_advances', 12, 2)->default(0);
                $table->decimal('net_salary', 12, 2)->default(0);
                $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
                $table->foreignId('wallet_id')->nullable();
                $table->foreignId('expense_id')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->foreignId('paid_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'year', 'month']);
            });
        }

        // 3. Create employee_adjustments table (Advances, Deductions, Bonuses, Allowances)
        if (!Schema::hasTable('employee_adjustments')) {
            Schema::create('employee_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable();
                $table->foreignId('branch_id')->nullable();
                $table->enum('type', ['advance', 'deduction', 'bonus', 'allowance', 'commission']);
                $table->decimal('amount', 12, 2);
                $table->date('date');
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'settled', 'cancelled'])->default('pending');
                $table->foreignId('payroll_id')->nullable();
                $table->foreignId('wallet_id')->nullable();
                $table->foreignId('created_by')->nullable();
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_adjustments');
        Schema::dropIfExists('payrolls');

        Schema::table('users', function (Blueprint $table) {
            $columns = ['hire_date', 'national_id', 'emergency_phone', 'salary_payment_day', 'salary_type'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
