<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmpresaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Empresa/Index', [
            'empresas' => Empresa::withCount(['estabelecimentos'])->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Empresa/Form', ['empresa' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);
        $empresa = Empresa::create($data);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa criada.');
    }

    public function show(Empresa $empresa): Response
    {
        $empresa->load(['estabelecimentos', 'usuarios']);

        return Inertia::render('Empresa/Show', ['empresa' => $empresa]);
    }

    public function edit(Empresa $empresa): Response
    {
        return Inertia::render('Empresa/Form', ['empresa' => $empresa]);
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $empresa->update($this->validar($request, $empresa));

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa atualizada.');
    }

    private function validar(Request $request, ?Empresa $empresa = null): array
    {
        return $request->validate([
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:18', 'unique:empresas,cnpj' . ($empresa ? ",{$empresa->id},id" : '')],
            'cnae' => ['nullable', 'string', 'max:10'],
            'grau_risco' => ['required', 'integer', 'between:1,4'],
            'responsavel_tecnico' => ['nullable', 'string', 'max:255'],
            'ativo' => ['boolean'],
        ]);
    }
}
