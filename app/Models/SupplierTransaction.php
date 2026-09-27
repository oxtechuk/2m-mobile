<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'type',
        'amount',
        'balance_after',
        'payment_method',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
