<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'cnae',
        'grau_risco',
        'responsavel_tecnico',
        'ativo',
    ];

    protected $casts = [
        'grau_risco' => 'integer',
        'ativo' => 'boolean',
    ];

    public function estabelecimentos(): HasMany
    {
        return $this->hasMany(Estabelecimento::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
