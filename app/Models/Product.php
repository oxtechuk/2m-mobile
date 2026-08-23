<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'description',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'minimum_stock',
        'unit',
        'has_serials',
        'image',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'has_serials' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        $defaultImg = setting('default_product_image');
        if ($defaultImg && \Illuminate\Support\Facades\Storage::disk('public')->exists($defaultImg)) {
            return asset('storage/' . $defaultImg);
        }

        return null;
    }
}
