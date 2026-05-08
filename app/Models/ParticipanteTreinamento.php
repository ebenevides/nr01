<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipanteTreinamento extends Model
{
    use BelongsToTenant, HasFactory, HasUuids;

    protected $table = 'participantes_treinamento';

    protected $fillable = [
        'empresa_id',
        'treinamento_id',
        'ghe_id',
        'nome',
        'cpf',
        'cargo',
        'valido_ate',
    ];

    protected $casts = [
        'valido_ate' => 'date',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function ghe(): BelongsTo
    {
        return $this->belongsTo(Ghe::class);
    }

    public function vencido(): bool
    {
        return $this->valido_ate->isPast();
    }

    public function diasParaVencer(): int
    {
        return (int) now()->diffInDays($this->valido_ate, false);
    }
}
