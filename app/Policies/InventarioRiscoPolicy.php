<?php

namespace App\Policies;

use App\Models\InventarioRisco;
use App\Models\User;
use App\Policies\Concerns\TenantPolicy;

class InventarioRiscoPolicy
{
    use TenantPolicy;
}
