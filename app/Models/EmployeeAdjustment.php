<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'type', // 'advance', 'deduction', 'bonus', 'allowance', 'commission'
        'amount',
        'date',
        'reason',
        'status', // 'pending', 'settled', 'cancelled'
        'payroll_id',
        'wallet_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
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

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeNameAttribute()
    {
        return match ($this->type) {
            'advance' => 'سلفة',
            'deduction' => 'خصم / جزاء',
            'bonus' => 'مكافأة / حافز',
            'allowance' => 'بدل إضافي',
            'commission' => 'عمولة',
            default => $this->type,
        };
    }

    public function getTypeBadgeClassAttribute()
    {
        return match ($this->type) {
            'advance' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'deduction' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
            'bonus' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            'allowance' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            'commission' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
            default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
        };
    }
}
