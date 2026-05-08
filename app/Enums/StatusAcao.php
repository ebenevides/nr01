<?php

namespace App\Enums;

enum StatusAcao: string
{
    case Pendente = 'pendente';
    case EmAndamento = 'em_andamento';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match($this) {
            self::Pendente => 'Pendente',
            self::EmAndamento => 'Em Andamento',
            self::Concluido => 'Concluído',
            self::Cancelado => 'Cancelado',
        };
    }
}
