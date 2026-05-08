<?php

namespace App\Services;

use App\Models\Evidencia;
use App\Models\PlanoAcao;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvidenciaService
{
    private const MIMES_PERMITIDOS = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    private const TAMANHO_MAX_BYTES = 20 * 1024 * 1024; // 20 MB

    public function upload(PlanoAcao $plano, UploadedFile $arquivo, ?string $descricao = null): Evidencia
    {
        $this->validar($arquivo);

        $chave = sprintf(
            'evidencias/%s/%s/%s',
            Auth::user()->empresa_id,
            $plano->id,
            Str::uuid() . '.' . $arquivo->extension()
        );

        Storage::disk('s3')->putFileAs(
            dirname($chave),
            $arquivo,
            basename($chave),
            ['visibility' => 'private']
        );

        return Evidencia::create([
            'empresa_id' => Auth::user()->empresa_id,
            'plano_acao_id' => $plano->id,
            'uploaded_by' => Auth::id(),
            'nome_arquivo' => $arquivo->getClientOriginalName(),
            'caminho_s3' => $chave,
            'tipo_mime' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'descricao' => $descricao,
        ]);
    }

    public function deletar(Evidencia $evidencia): void
    {
        Storage::disk('s3')->delete($evidencia->caminho_s3);
        $evidencia->delete();
    }

    private function validar(UploadedFile $arquivo): void
    {
        if (! in_array($arquivo->getMimeType(), self::MIMES_PERMITIDOS)) {
            throw new \InvalidArgumentException('Tipo de arquivo não permitido.');
        }

        if ($arquivo->getSize() > self::TAMANHO_MAX_BYTES) {
            throw new \InvalidArgumentException('Arquivo excede limite de 20 MB.');
        }
    }
}
