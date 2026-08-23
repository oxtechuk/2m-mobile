<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'product_id',
        'quantity',
        'unit_cost',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
