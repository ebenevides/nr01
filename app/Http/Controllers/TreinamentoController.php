<?php

namespace App\Http\Controllers;

use App\Actions\Treinamento\CriarTreinamento;
use App\Actions\Treinamento\RegistrarParticipantes;
use App\Enums\TipoTreinamento;
use App\Models\Ghe;
use App\Models\ParticipanteTreinamento;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TreinamentoController extends Controller
{
    public function index(): Response
    {
        $treinamentos = Treinamento::withCount('participantes')
            ->latest('data_realizacao')
            ->paginate(20);

        $vencendo = ParticipanteTreinamento::whereBetween('valido_ate', [now(), now()->addDays(30)])
            ->with('treinamento')
            ->get();

        return Inertia::render('Treinamento/Index', [
            'treinamentos' => $treinamentos,
            'vencendo' => $vencendo,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Treinamento::class);

        return Inertia::render('Treinamento/Form', [
            'tipos' => collect(TipoTreinamento::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]),
            'ghes' => Ghe::where('ativo', true)->select('id', 'nome')->get(),
        ]);
    }

    public function store(Request $request, CriarTreinamento $action): RedirectResponse
    {
        $this->authorize('create', Treinamento::class);

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'in:' . implode(',', array_column(TipoTreinamento::cases(), 'value'))],
            'carga_horaria' => ['required', 'integer', 'min:1'],
            'data_realizacao' => ['required', 'date'],
            'validade_meses' => ['required', 'integer', 'min:1', 'max:60'],
            'instrutor' => ['required', 'string', 'max:255'],
            'local' => ['nullable', 'string', 'max:255'],
            'lista_presenca' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $treinamento = $action->handle($data, $request->file('lista_presenca'));

        return redirect()->route('treinamentos.show', $treinamento)
            ->with('success', 'Treinamento registrado.');
    }

    public function show(Treinamento $treinamento): Response
    {
        $treinamento->load('participantes.ghe');

        return Inertia::render('Treinamento/Show', [
            'treinamento' => $treinamento,
            'listaPresencaUrl' => $treinamento->urlListaPresenca(),
            'ghes' => Ghe::where('ativo', true)->select('id', 'nome')->get(),
        ]);
    }

    public function storeParticipantes(Request $request, Treinamento $treinamento, RegistrarParticipantes $action): RedirectResponse
    {
        $this->authorize('update', $treinamento);

        $data = $request->validate([
            'participantes' => ['required', 'array', 'min:1'],
            'participantes.*.nome' => ['required', 'string', 'max:255'],
            'participantes.*.cpf' => ['nullable', 'string', 'max:14'],
            'participantes.*.cargo' => ['nullable', 'string', 'max:100'],
            'participantes.*.ghe_id' => ['nullable', 'uuid', 'exists:ghes,id'],
        ]);

        $action->handle($treinamento, $data['participantes']);

        return back()->with('success', 'Participantes registrados.');
    }

    public function destroyParticipante(Treinamento $treinamento, ParticipanteTreinamento $participante): RedirectResponse
    {
        $this->authorize('update', $treinamento);

        $participante->delete();

        return back()->with('success', 'Participante removido.');
    }
}
