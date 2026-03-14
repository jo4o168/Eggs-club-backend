<?php

namespace App\Enum;

enum SubscriptionFrequency: int
{
    case WEEKLY = 0;

    case BI_WEEKLY = 1;

    case MONTHLY = 2;
}
