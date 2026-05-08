<?php

namespace App\Models;

use App\Enums\TipoTreinamento;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Treinamento extends Model
{
    use BelongsToTenant, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'titulo',
        'tipo',
        'carga_horaria',
        'data_realizacao',
        'validade_meses',
        'instrutor',
        'local',
        'lista_presenca_s3',
        'observacoes',
    ];

    protected $casts = [
        'tipo' => TipoTreinamento::class,
        'data_realizacao' => 'date',
        'carga_horaria' => 'integer',
        'validade_meses' => 'integer',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipanteTreinamento::class);
    }

    public function urlListaPresenca(int $minutes = 30): ?string
    {
        if (! $this->lista_presenca_s3) return null;
        return Storage::disk('s3')->temporaryUrl($this->lista_presenca_s3, now()->addMinutes($minutes));
    }

    public function validoAte(): \Carbon\Carbon
    {
        return $this->data_realizacao->addMonths($this->validade_meses);
    }
}
