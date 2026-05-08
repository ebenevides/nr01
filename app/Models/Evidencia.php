<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Evidencia extends Model
{
    use BelongsToTenant, HasFactory, HasUuids;

    protected $fillable = [
        'empresa_id',
        'plano_acao_id',
        'uploaded_by',
        'nome_arquivo',
        'caminho_s3',
        'tipo_mime',
        'tamanho_bytes',
        'descricao',
    ];

    protected $casts = [
        'tamanho_bytes' => 'integer',
    ];

    public function planoAcao(): BelongsTo
    {
        return $this->belongsTo(PlanoAcao::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function urlTemporaria(int $minutes = 30): string
    {
        return Storage::disk('s3')->temporaryUrl($this->caminho_s3, now()->addMinutes($minutes));
    }
}
