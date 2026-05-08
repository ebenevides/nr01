<?php

namespace App\Actions\PlanoAcao;

use App\Models\Perigo;
use App\Models\PlanoAcao;
use Illuminate\Support\Facades\Auth;

class CriarPlanoAcao
{
    public function handle(Perigo $perigo, array $data): PlanoAcao
    {
        return PlanoAcao::create([
            'empresa_id' => Auth::user()->empresa_id,
            'perigo_id' => $perigo->id,
            'inventario_risco_id' => $perigo->inventario_risco_id,
            'descricao_acao' => $data['descricao_acao'],
            'tipo_controle' => $data['tipo_controle'],
            'responsavel' => $data['responsavel'],
            'prazo' => $data['prazo'],
            'observacoes' => $data['observacoes'] ?? null,
        ]);
    }
}
