<?php

namespace App\Enum;

enum DeliveryIssueReason: string
{
    case BROKEN = 'broken';
    case SPOILED = 'spoiled';
    case MISSING = 'missing';
    case OTHER = 'other';
}
