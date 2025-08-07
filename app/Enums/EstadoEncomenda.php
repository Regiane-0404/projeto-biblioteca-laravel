<?php

namespace App\Enums;

// A sintaxe ": string" define que este Enum é do tipo string.
// É o equivalente a usar a flag --type=string.
enum EstadoEncomenda: string
{
    case PENDENTE = 'pendente';
    case PAGO = 'pago';
    case ENVIADO = 'enviado';
    case ENTREGUE = 'entregue';
    case CANCELADO = 'cancelado';
}
