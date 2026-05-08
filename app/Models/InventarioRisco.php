<?php

namespace App\Models;

use App\Enums\StatusInventario;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventarioRisco extends Model
{
    use Auditable, BelongsToTenant, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inventarios_risco';

    protected $fillable = [
        'empresa_id',
        'ghe_id',
        'estabelecimento_id',
        'data_avaliacao',
        'responsavel',
        'status',
        'versao',
        'observacoes',
    ];

    protected $casts = [
        'data_avaliacao' => 'date',
        'status' => StatusInventario::class,
        'versao' => 'integer',
    ];

    public function ghe(): BelongsTo
    {
        return $this->belongsTo(Ghe::class);
    }

    public function estabelecimento(): BelongsTo
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function perigos(): HasMany
    {
        return $this->hasMany(Perigo::class);
    }
}
