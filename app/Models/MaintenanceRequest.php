<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'ticket_number',
        'branch_id',
        'customer_id',
        'technician_id',
        'device_type',
        'device_model',
        'device_serial',
        'problem_description',
        'diagnosis',
        'pre_repair_checklist',
        'estimated_cost',
        'final_cost',
        'advance_payment',
        'status',
        'priority',
        'estimated_delivery',
        'delivered_at',
    ];

    protected $casts = [
        'pre_repair_checklist' => 'array',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'estimated_delivery' => 'date',
        'delivered_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(MaintenanceStatusLog::class);
    }

    public function spareParts()
    {
        return $this->hasMany(MaintenanceSparePart::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
