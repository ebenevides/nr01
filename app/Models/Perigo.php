<?php

namespace App\Models;

use App\Enums\GrupoRisco;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perigo extends Model
{
    use Auditable, BelongsToTenant, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'inventario_risco_id',
        'grupo_risco',
        'descricao_perigo',
        'fonte_geradora',
        'trabalhadores_expostos',
        'probabilidade',
        'severidade',
        'medidas_existentes',
    ];

    protected $casts = [
        'grupo_risco' => GrupoRisco::class,
        'probabilidade' => 'integer',
        'severidade' => 'integer',
        'nivel_risco' => 'integer',
        'trabalhadores_expostos' => 'integer',
    ];

    public function inventarioRisco(): BelongsTo
    {
        return $this->belongsTo(InventarioRisco::class);
    }

    public function nivelLabel(): string
    {
        return match(true) {
            $this->nivel_risco <= 2 => 'Aceitável',
            $this->nivel_risco <= 4 => 'Tolerável',
            default => 'Inaceitável',
        };
    }
}
