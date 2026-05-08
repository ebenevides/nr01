<?php

namespace App\Models;

use App\Enums\TipoDocumento;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use BelongsToTenant, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'gerado_por',
        'tipo',
        'versao',
        'status',
        'caminho_s3',
        'erro_msg',
    ];

    protected $casts = [
        'tipo' => TipoDocumento::class,
        'versao' => 'integer',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function gerador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerado_por');
    }

    public function urlTemporaria(int $minutes = 60): ?string
    {
        if (! $this->caminho_s3) return null;
        return Storage::disk('s3')->temporaryUrl($this->caminho_s3, now()->addMinutes($minutes));
    }

    public function pronto(): bool
    {
        return $this->status === 'concluido' && $this->caminho_s3 !== null;
    }
}
