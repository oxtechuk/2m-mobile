<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'primary_supplier_id');
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class)->orderBy('id', 'desc');
    }
}
