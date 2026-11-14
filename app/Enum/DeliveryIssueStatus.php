<?php

namespace App\Enum;

enum DeliveryIssueStatus: string
{
    case OPEN = 'open';
    case REFUNDED = 'refunded';
    case REPLACED = 'replaced';
    case REJECTED = 'rejected';
}
