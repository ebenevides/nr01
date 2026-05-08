<?php

namespace App\Policies;

use App\Models\PlanoAcao;
use App\Models\User;
use App\Policies\Concerns\TenantPolicy;

class PlanoAcaoPolicy
{
    use TenantPolicy;

    /** gestor pode atualizar status mas não criar/editar plano */
    public function updateStatus(User $user, PlanoAcao $plano): bool
    {
        return in_array($user->role, ['admin_empresa', 'tecnico_seguranca', 'gestor']);
    }

    public function uploadEvidencia(User $user, PlanoAcao $plano): bool
    {
        return $user->canWrite();
    }
}
