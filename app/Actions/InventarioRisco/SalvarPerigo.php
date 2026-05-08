<?php

namespace App\Actions\InventarioRisco;

use App\Models\InventarioRisco;
use App\Models\Perigo;
use Illuminate\Support\Facades\Auth;

class SalvarPerigo
{
    public function handle(InventarioRisco $inventario, array $data, ?Perigo $perigo = null): Perigo
    {
        $attributes = [
            'empresa_id' => Auth::user()->empresa_id,
            'inventario_risco_id' => $inventario->id,
            'grupo_risco' => $data['grupo_risco'],
            'descricao_perigo' => $data['descricao_perigo'],
            'fonte_geradora' => $data['fonte_geradora'] ?? null,
            'trabalhadores_expostos' => $data['trabalhadores_expostos'] ?? 0,
            'probabilidade' => $data['probabilidade'],
            'severidade' => $data['severidade'],
            'medidas_existentes' => $data['medidas_existentes'] ?? null,
        ];

        if ($perigo) {
            $perigo->update($attributes);
            return $perigo->fresh();
        }

        return Perigo::create($attributes);
    }
}
