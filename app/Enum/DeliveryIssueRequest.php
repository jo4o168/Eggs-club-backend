<?php

namespace App\Enum;

enum DeliveryIssueRequest: string
{
    case REFUND = 'refund';
    case REPLACEMENT = 'replacement';
}
