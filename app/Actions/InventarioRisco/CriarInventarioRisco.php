<?php

namespace App\Actions\InventarioRisco;

use App\Models\InventarioRisco;
use Illuminate\Support\Facades\Auth;

class CriarInventarioRisco
{
    public function handle(array $data): InventarioRisco
    {
        return InventarioRisco::create([
            'empresa_id' => Auth::user()->empresa_id,
            'ghe_id' => $data['ghe_id'],
            'estabelecimento_id' => $data['estabelecimento_id'],
            'data_avaliacao' => $data['data_avaliacao'],
            'responsavel' => $data['responsavel'],
            'observacoes' => $data['observacoes'] ?? null,
        ]);
    }
}
