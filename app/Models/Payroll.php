<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'year',
        'month',
        'basic_salary',
        'total_allowances',
        'total_bonuses',
        'total_commissions',
        'total_deductions',
        'total_advances',
        'net_salary',
        'status', // 'draft', 'approved', 'paid'
        'wallet_id',
        'expense_id',
        'approved_at',
        'approved_by',
        'paid_at',
        'paid_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'total_allowances' => 'decimal:2',
            'total_bonuses' => 'decimal:2',
            'total_commissions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'total_advances' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function adjustments()
    {
        return $this->hasMany(EmployeeAdjustment::class);
    }

    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$this->month] ?? $this->month;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'paid' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            'approved' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            'draft' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            default => 'bg-gray-500/10 text-gray-400 border border-gray-500/20',
        };
    }

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            'paid' => 'تم الصرف',
            'approved' => 'معتمد للصرف',
            'draft' => 'مسودة قيد المراجعة',
            default => $this->status,
        };
    }
}
