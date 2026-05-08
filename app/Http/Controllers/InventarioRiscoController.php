<?php

namespace App\Http\Controllers;

use App\Actions\InventarioRisco\CriarInventarioRisco;
use App\Actions\InventarioRisco\SalvarPerigo;
use App\Enums\GrupoRisco;
use App\Models\Estabelecimento;
use App\Models\Ghe;
use App\Models\InventarioRisco;
use App\Models\Perigo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventarioRiscoController extends Controller
{
    public function index(): Response
    {
        $inventarios = InventarioRisco::with(['ghe', 'estabelecimento'])
            ->latest()
            ->paginate(20);

        return Inertia::render('InventarioRisco/Index', [
            'inventarios' => $inventarios,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', InventarioRisco::class);

        return Inertia::render('InventarioRisco/Form', [
            'ghes' => Ghe::where('ativo', true)->with('estabelecimento')->get(),
            'estabelecimentos' => Estabelecimento::where('ativo', true)->get(),
        ]);
    }

    public function store(Request $request, CriarInventarioRisco $action): RedirectResponse
    {
        $this->authorize('create', InventarioRisco::class);

        $data = $request->validate([
            'ghe_id' => ['required', 'uuid', 'exists:ghes,id'],
            'estabelecimento_id' => ['required', 'uuid', 'exists:estabelecimentos,id'],
            'data_avaliacao' => ['required', 'date'],
            'responsavel' => ['required', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $inventario = $action->handle($data);

        return redirect()->route('inventarios.show', $inventario)
            ->with('success', 'Inventário criado com sucesso.');
    }

    public function show(InventarioRisco $inventario): Response
    {
        $inventario->load(['ghe', 'estabelecimento', 'perigos']);

        return Inertia::render('InventarioRisco/Show', [
            'inventario' => $inventario,
            'gruposRisco' => collect(GrupoRisco::cases())->map(fn($g) => [
                'value' => $g->value,
                'label' => $g->label(),
            ]),
        ]);
    }

    public function storePerigo(Request $request, InventarioRisco $inventario, SalvarPerigo $action): RedirectResponse
    {
        $this->authorize('update', $inventario);

        $data = $request->validate([
            'grupo_risco' => ['required', 'string', 'in:' . implode(',', array_column(GrupoRisco::cases(), 'value'))],
            'descricao_perigo' => ['required', 'string'],
            'fonte_geradora' => ['nullable', 'string'],
            'trabalhadores_expostos' => ['nullable', 'integer', 'min:0'],
            'probabilidade' => ['required', 'integer', 'between:1,3'],
            'severidade' => ['required', 'integer', 'between:1,3'],
            'medidas_existentes' => ['nullable', 'string'],
        ]);

        $action->handle($inventario, $data);

        return back()->with('success', 'Perigo registrado.');
    }

    public function updatePerigo(Request $request, InventarioRisco $inventario, Perigo $perigo, SalvarPerigo $action): RedirectResponse
    {
        $this->authorize('update', $inventario);

        $data = $request->validate([
            'grupo_risco' => ['required', 'string', 'in:' . implode(',', array_column(GrupoRisco::cases(), 'value'))],
            'descricao_perigo' => ['required', 'string'],
            'fonte_geradora' => ['nullable', 'string'],
            'trabalhadores_expostos' => ['nullable', 'integer', 'min:0'],
            'probabilidade' => ['required', 'integer', 'between:1,3'],
            'severidade' => ['required', 'integer', 'between:1,3'],
            'medidas_existentes' => ['nullable', 'string'],
        ]);

        $action->handle($inventario, $data, $perigo);

        return back()->with('success', 'Perigo atualizado.');
    }

    public function destroyPerigo(InventarioRisco $inventario, Perigo $perigo): RedirectResponse
    {
        $this->authorize('update', $inventario);

        $perigo->delete();

        return back()->with('success', 'Perigo removido.');
    }
}
