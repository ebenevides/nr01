<?php

namespace App\Services;

use App\Enums\TipoDocumento;
use App\Models\Documento;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoService
{
    public function gerar(Documento $documento): void
    {
        try {
            $empresa = Empresa::findOrFail($documento->empresa_id);

            $pdf = match($documento->tipo) {
                TipoDocumento::Pgr => $this->gerarPgr($empresa),
                TipoDocumento::Ltcat => $this->gerarLtcat($empresa),
            };

            $chave = sprintf(
                'documentos/%s/%s/v%d-%s-%s.pdf',
                $empresa->id,
                $documento->tipo->value,
                $documento->versao,
                $documento->tipo->nomeArquivo(),
                now()->format('Ymd')
            );

            Storage::disk('s3')->put($chave, $pdf->output(), ['visibility' => 'private']);

            $documento->update([
                'status' => 'concluido',
                'caminho_s3' => $chave,
            ]);
        } catch (\Throwable $e) {
            $documento->update([
                'status' => 'erro',
                'erro_msg' => $e->getMessage(),
            ]);
        }
    }

    private function gerarPgr(Empresa $empresa): \Barryvdh\DomPDF\PDF
    {
        $empresa->load([
            'estabelecimentos.ghes.inventariosRisco.perigos',
            'estabelecimentos.ghes.inventariosRisco.planoAcao',
        ]);

        return Pdf::loadView('pdf.pgr', compact('empresa'))
            ->setPaper('A4', 'portrait');
    }

    private function gerarLtcat(Empresa $empresa): \Barryvdh\DomPDF\PDF
    {
        $empresa->load([
            'estabelecimentos.ghes.inventariosRisco.perigos',
        ]);

        return Pdf::loadView('pdf.ltcat', compact('empresa'))
            ->setPaper('A4', 'portrait');
    }
}
