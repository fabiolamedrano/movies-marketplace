<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pendiente  = 'pendiente';
    case Enviado    = 'enviado';
    case Completado = 'completado';
    case Cancelado  = 'cancelado';
}