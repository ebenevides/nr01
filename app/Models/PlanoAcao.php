<?php

namespace App\Models;

use App\Enums\StatusAcao;
use App\Enums\TipoControle;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoAcao extends Model
{
    use Auditable, BelongsToTenant, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'planos_acao';

    protected $fillable = [
        'empresa_id',
        'perigo_id',
        'inventario_risco_id',
        'descricao_acao',
        'tipo_controle',
        'responsavel',
        'prazo',
        'status',
        'observacoes',
        'concluido_em',
    ];

    protected $casts = [
        'prazo' => 'date',
        'concluido_em' => 'datetime',
        'status' => StatusAcao::class,
        'tipo_controle' => TipoControle::class,
    ];

    public function perigo(): BelongsTo
    {
        return $this->belongsTo(Perigo::class);
    }

    public function inventarioRisco(): BelongsTo
    {
        return $this->belongsTo(InventarioRisco::class);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function vencido(): bool
    {
        return $this->status !== StatusAcao::Concluido
            && $this->prazo->isPast();
    }
}
