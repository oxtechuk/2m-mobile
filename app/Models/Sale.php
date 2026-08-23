<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Sale extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'invoice_number',
        'cash_shift_id',
        'branch_id',
        'customer_id',
        'cashier_id',
        'subtotal',
        'discount_amount',
        'discount_type',
        'tax_rate',
        'tax_amount',
        'total',
        'paid_amount',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function cashShift()
    {
        return $this->belongsTo(CashShift::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
