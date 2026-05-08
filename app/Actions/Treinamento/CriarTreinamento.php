<?php

namespace App\Actions\Treinamento;

use App\Models\Treinamento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CriarTreinamento
{
    public function handle(array $data, ?UploadedFile $listaPresenca = null): Treinamento
    {
        $listaPresencaS3 = null;

        if ($listaPresenca) {
            $chave = sprintf(
                'treinamentos/%s/%s/lista-presenca.%s',
                Auth::user()->empresa_id,
                Str::uuid(),
                $listaPresenca->extension()
            );
            Storage::disk('s3')->putFileAs(dirname($chave), $listaPresenca, basename($chave), ['visibility' => 'private']);
            $listaPresencaS3 = $chave;
        }

        return Treinamento::create([
            'empresa_id' => Auth::user()->empresa_id,
            'titulo' => $data['titulo'],
            'tipo' => $data['tipo'],
            'carga_horaria' => $data['carga_horaria'],
            'data_realizacao' => $data['data_realizacao'],
            'validade_meses' => $data['validade_meses'] ?? 12,
            'instrutor' => $data['instrutor'],
            'local' => $data['local'] ?? null,
            'lista_presenca_s3' => $listaPresencaS3,
            'observacoes' => $data['observacoes'] ?? null,
        ]);
    }
}
