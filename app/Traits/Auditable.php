<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'create', null, $model->getAttributes());
        });

        static::updating(function ($model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newValues = $model->getDirty();
            self::logAction($model, 'update', $oldValues, $newValues);
        });

        static::deleted(function ($model) {
            self::logAction($model, 'delete', $model->getAttributes(), null);
        });
    }

    protected static function logAction($model, $action, $oldValues = null, $newValues = null)
    {
        // Don't log audit_logs table itself to avoid infinite loops
        if ($model instanceof AuditLog) {
            return;
        }

        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silence log errors in production to avoid crashing requests
            logger()->error('Audit Log failed: ' . $e->getMessage());
        }
    }
}
