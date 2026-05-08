<?php

namespace App\Actions\Treinamento;

use App\Models\ParticipanteTreinamento;
use App\Models\Treinamento;
use Illuminate\Support\Facades\Auth;

class RegistrarParticipantes
{
    public function handle(Treinamento $treinamento, array $participantes): void
    {
        $validoAte = $treinamento->data_realizacao->addMonths($treinamento->validade_meses);

        foreach ($participantes as $p) {
            ParticipanteTreinamento::updateOrCreate(
                [
                    'treinamento_id' => $treinamento->id,
                    'cpf' => $p['cpf'] ?? null,
                ],
                [
                    'empresa_id' => Auth::user()->empresa_id,
                    'ghe_id' => $p['ghe_id'] ?? null,
                    'nome' => $p['nome'],
                    'cargo' => $p['cargo'] ?? null,
                    'valido_ate' => $validoAte,
                ]
            );
        }
    }
}
