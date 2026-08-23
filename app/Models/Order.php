<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_governorate',
        'shipping_city',
        'shipping_fee',
        'subtotal',
        'discount_total',
        'total',
        'coupon_code',
        'payment_method',
        'payment_status',
        'payment_id',
        'payment_screenshot',
        'status',
        'notes',
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
}
