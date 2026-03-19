<?php

enum StatusSolicitacao: string
{
    case ABERTA         = 'Aberta';
    case EM_ATENDIMENTO = 'Em Atendimento';
    case PAUSADA        = 'Pausada';
    case CONCLUIDA      = 'Concluída';
    case CANCELADA      = 'Cancelada';

    public function isFinalizado(): bool
    {
        return $this === self::CONCLUIDA || $this === self::CANCELADA;
    }
}
