<?php

namespace App\Enum;

enum OrderStatus: int
{
    case PENDING = 0;

    case CONFIRMED = 1;

    case PREPARING = 2;

    case SHIPPED = 3;

    case DELIVERED = 4;

    case CANCELLED = 5;

    public function customerNotice(): ?string
    {
        return match ($this) {
            self::PENDING => null,
            self::CONFIRMED => 'Seu pedido foi confirmado e está em preparação.',
            self::PREPARING => 'O produtor está preparando o seu kit.',
            self::SHIPPED => 'Seu pedido saiu para entrega.',
            self::DELIVERED => 'Seu pedido foi entregue.',
            self::CANCELLED => null,
        };
    }
}
