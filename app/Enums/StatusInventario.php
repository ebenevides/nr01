<?php

namespace App\Enums;

enum StatusInventario: string
{
    case Rascunho = 'rascunho';
    case EmRevisao = 'em_revisao';
    case Aprovado = 'aprovado';

    public function label(): string
    {
        return match($this) {
            self::Rascunho => 'Rascunho',
            self::EmRevisao => 'Em Revisão',
            self::Aprovado => 'Aprovado',
        };
    }
}
