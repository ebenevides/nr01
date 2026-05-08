<?php

namespace App\Http\Controllers;

use App\Enums\TipoDocumento;
use App\Jobs\GerarDocumentoJob;
use App\Models\Documento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DocumentoController extends Controller
{
    public function index(): Response
    {
        $documentos = Documento::with('gerador')
            ->latest()
            ->paginate(20);

        return Inertia::render('Documento/Index', [
            'documentos' => $documentos,
            'tipos' => collect(TipoDocumento::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Documento::class);

        $data = $request->validate([
            'tipo' => ['required', 'string', 'in:' . implode(',', array_column(TipoDocumento::cases(), 'value'))],
        ]);

        $versao = Documento::where('empresa_id', Auth::user()->empresa_id)
            ->where('tipo', $data['tipo'])
            ->max('versao') + 1;

        $documento = Documento::create([
            'empresa_id' => Auth::user()->empresa_id,
            'gerado_por' => Auth::id(),
            'tipo' => $data['tipo'],
            'versao' => $versao,
            'status' => 'gerando',
        ]);

        GerarDocumentoJob::dispatch($documento);

        return back()->with('success', 'Geração iniciada. O documento estará disponível em instantes.');
    }

    public function download(Documento $documento): RedirectResponse
    {
        $this->authorize('download', $documento);
        abort_unless($documento->pronto(), 404);

        return redirect($documento->urlTemporaria(30));
    }
}
