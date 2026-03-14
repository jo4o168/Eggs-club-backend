<?php

namespace App\Enum;

enum SubscriptionStatus: int
{
    case ACTIVE = 0;

    case PAUSED = 1;

    case CANCELLED = 2;

    case EXPIRED = 3;
}
