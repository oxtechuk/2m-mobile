<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'national_id', 'emergency_phone', 'branch_id', 'role', 'is_active', 'avatar', 'salary', 'commission_rate', 'hire_date', 'salary_payment_day', 'salary_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'salary' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'hire_date' => 'date',
            'salary_payment_day' => 'integer',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'technician_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'performed_by');
    }

    public function adjustments()
    {
        return $this->hasMany(EmployeeAdjustment::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function pendingAdvancesTotal(): float
    {
        return (float) $this->adjustments()
            ->where('type', 'advance')
            ->where('status', 'pending')
            ->sum('amount');
    }
}

