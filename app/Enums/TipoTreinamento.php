<?php

namespace App\Enums;

enum TipoTreinamento: string
{
    case Integracao = 'integracao';
    case Nr01 = 'nr01';
    case PrimeirosSocorros = 'primeiros_socorros';
    case UsoEpi = 'uso_epi';
    case Outro = 'outro';

    public function label(): string
    {
        return match($this) {
            self::Integracao => 'Integração',
            self::Nr01 => 'NR-01 / Gerenciamento de Riscos',
            self::PrimeirosSocorros => 'Primeiros Socorros',
            self::UsoEpi => 'Uso de EPI',
            self::Outro => 'Outro',
        };
    }
}
