<?php

namespace App\Enums;

enum TipoControle: string
{
    case Eliminacao = 'eliminacao';
    case Substituicao = 'substituicao';
    case ControleEngenharia = 'controle_engenharia';
    case ControleAdministrativo = 'controle_administrativo';
    case Epi = 'epi';

    public function label(): string
    {
        return match($this) {
            self::Eliminacao => 'Eliminação',
            self::Substituicao => 'Substituição',
            self::ControleEngenharia => 'Controle de Engenharia',
            self::ControleAdministrativo => 'Controle Administrativo',
            self::Epi => 'EPI',
        };
    }

    public function prioridade(): int
    {
        return match($this) {
            self::Eliminacao => 1,
            self::Substituicao => 2,
            self::ControleEngenharia => 3,
            self::ControleAdministrativo => 4,
            self::Epi => 5,
        };
    }
}
