<?php

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;
use App\Policies\Concerns\TenantPolicy;

class DocumentoPolicy
{
    use TenantPolicy;

    /** gerar PDF = tecnico ou admin apenas */
    public function create(User $user): bool
    {
        return $user->canWrite();
    }

    /** todos podem fazer download */
    public function download(User $user, Documento $documento): bool
    {
        return true;
    }
}
