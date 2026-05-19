<?php

namespace App\Enum;

enum CartPurchaseMode: string
{
    case ONE_TIME = 'one_time';

    case SUBSCRIPTION = 'subscription';
}
