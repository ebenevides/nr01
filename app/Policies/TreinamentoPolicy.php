<?php

namespace App\Policies;

use App\Models\Treinamento;
use App\Models\User;
use App\Policies\Concerns\TenantPolicy;

class TreinamentoPolicy
{
    use TenantPolicy;
}
