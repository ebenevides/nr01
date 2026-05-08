<?php

namespace App\Enums;

enum TipoDocumento: string
{
    case Pgr = 'pgr';
    case Ltcat = 'ltcat';

    public function label(): string
    {
        return match($this) {
            self::Pgr => 'PGR — Programa de Gerenciamento de Riscos',
            self::Ltcat => 'LTCAT — Laudo Técnico das Condições Ambientais do Trabalho',
        };
    }

    public function nomeArquivo(): string
    {
        return strtoupper($this->value);
    }
}
