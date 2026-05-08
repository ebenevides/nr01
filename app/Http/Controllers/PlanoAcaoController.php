<?php

namespace App\Http\Controllers;

use App\Actions\PlanoAcao\AtualizarStatusAcao;
use App\Actions\PlanoAcao\CriarPlanoAcao;
use App\Enums\StatusAcao;
use App\Enums\TipoControle;
use App\Models\Perigo;
use App\Models\PlanoAcao;
use App\Services\EvidenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanoAcaoController extends Controller
{
    public function index(): Response
    {
        $planos = PlanoAcao::with(['perigo.inventarioRisco.ghe', 'evidencias'])
            ->latest()
            ->paginate(20);

        return Inertia::render('PlanoAcao/Index', [
            'planos' => $planos,
            'statuses' => collect(StatusAcao::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function store(Request $request, Perigo $perigo, CriarPlanoAcao $action): RedirectResponse
    {
        $this->authorize('create', PlanoAcao::class);

        $data = $request->validate([
            'descricao_acao' => ['required', 'string'],
            'tipo_controle' => ['required', 'string', 'in:' . implode(',', array_column(TipoControle::cases(), 'value'))],
            'responsavel' => ['required', 'string', 'max:255'],
            'prazo' => ['required', 'date', 'after:today'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $action->handle($perigo, $data);

        return back()->with('success', 'Plano de ação criado.');
    }

    public function updateStatus(Request $request, PlanoAcao $plano, AtualizarStatusAcao $action): RedirectResponse
    {
        $this->authorize('updateStatus', $plano);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_column(StatusAcao::cases(), 'value'))],
        ]);

        $action->handle($plano, StatusAcao::from($data['status']));

        return back()->with('success', 'Status atualizado.');
    }

    public function uploadEvidencia(Request $request, PlanoAcao $plano, EvidenciaService $service): RedirectResponse
    {
        $this->authorize('uploadEvidencia', $plano);

        $request->validate([
            'arquivo' => ['required', 'file', 'max:20480'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $service->upload($plano, $request->file('arquivo'), $request->descricao);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['arquivo' => $e->getMessage()]);
        }

        return back()->with('success', 'Evidência anexada.');
    }

    public function destroyEvidencia(PlanoAcao $plano, \App\Models\Evidencia $evidencia, EvidenciaService $service): RedirectResponse
    {
        $this->authorize('uploadEvidencia', $plano);

        $service->deletar($evidencia);

        return back()->with('success', 'Evidência removida.');
    }
}
