<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (! $model->empresa_id && auth()->check()) {
                $model->empresa_id = auth()->user()->empresa_id;
            }
        });
    }

    public function scopeTenant(Builder $query): Builder
    {
        return $query->where('empresa_id', auth()->user()?->empresa_id);
    }
}
