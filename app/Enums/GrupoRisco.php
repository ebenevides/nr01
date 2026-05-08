<?php

namespace App\Enums;

enum GrupoRisco: string
{
    case Fisico = 'fisico';
    case Quimico = 'quimico';
    case Biologico = 'biologico';
    case Ergonomico = 'ergonomico';
    case Acidente = 'acidente';

    public function label(): string
    {
        return match($this) {
            self::Fisico => 'Físico',
            self::Quimico => 'Químico',
            self::Biologico => 'Biológico',
            self::Ergonomico => 'Ergonômico',
            self::Acidente => 'Acidente',
        };
    }
}
