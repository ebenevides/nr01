<?php

namespace App\Jobs;

use App\Models\Documento;
use App\Services\DocumentoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GerarDocumentoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(public readonly Documento $documento)
    {
        $this->onQueue('documentos');
    }

    public function handle(DocumentoService $service): void
    {
        $service->gerar($this->documento);
    }
}
