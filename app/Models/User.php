<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'empresa_id', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'empresa_id' => 'string',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin_empresa';
    }

    public function isTecnico(): bool
    {
        return $this->role === 'tecnico_seguranca';
    }

    public function isGestor(): bool
    {
        return $this->role === 'gestor';
    }

    /** admin ou tecnico — pode criar/editar conteúdo NR-01 */
    public function canWrite(): bool
    {
        return in_array($this->role, ['admin_empresa', 'tecnico_seguranca']);
    }

    public function hasTenant(): bool
    {
        return $this->empresa_id !== null;
    }
}
