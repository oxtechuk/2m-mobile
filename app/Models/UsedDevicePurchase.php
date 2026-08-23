<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedDevicePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'device_name',
        'imei',
        'purchase_price',
        'estimated_repair_cost',
        'status',
        'national_id_photo',
        'recorded_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'estimated_repair_cost' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
