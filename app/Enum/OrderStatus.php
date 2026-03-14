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
}
