<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'token',
        'download_count',
        'max_downloads',
        'expires_at',
    ];

    protected $casts = [
        'download_count' => 'integer',
        'max_downloads' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper helper to check if download is active/valid
    public function isValid()
    {
        if ($this->max_downloads !== null && $this->download_count >= $this->max_downloads) {
            return false;
        }

        if ($this->expires_at !== null && now()->greaterThan($this->expires_at)) {
            return false;
        }

        return true;
    }
}
