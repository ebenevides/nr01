<?php

namespace App\Actions\PlanoAcao;

use App\Enums\StatusAcao;
use App\Models\PlanoAcao;

class AtualizarStatusAcao
{
    public function handle(PlanoAcao $plano, StatusAcao $status): PlanoAcao
    {
        $plano->update([
            'status' => $status,
            'concluido_em' => $status === StatusAcao::Concluido ? now() : null,
        ]);

        return $plano->fresh();
    }
}
