<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn($model) => static::registrarAudit('created', null, $model->getAttributes(), $model));
        static::updated(fn($model) => static::registrarAudit('updated', $model->getOriginal(), $model->getChanges(), $model));
        static::deleted(fn($model) => static::registrarAudit('deleted', $model->getAttributes(), null, $model));
    }

    private static function registrarAudit(string $event, ?array $old, ?array $new, $model): void
    {
        // Ignorar campos técnicos no diff
        $ignorar = ['updated_at', 'created_at', 'deleted_at', 'remember_token', 'password'];

        if ($old) $old = array_diff_key($old, array_flip($ignorar));
        if ($new) $new = array_diff_key($new, array_flip($ignorar));

        AuditLog::create([
            'empresa_id' => $model->empresa_id ?? Auth::user()?->empresa_id,
            'user_id' => Auth::id(),
            'event' => $event,
            'model_type' => get_class($model),
            'model_id' => (string) $model->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
