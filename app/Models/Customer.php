<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'phone',
        'secondary_phone',
        'email',
        'address',
        'branch_id',
        'notes',
        'total_purchases',
        'total_repairs',
        'loyalty_points',
    ];

    protected $casts = [
        'total_purchases' => 'decimal:2',
        'total_repairs' => 'decimal:2',
        'loyalty_points' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
