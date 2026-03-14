<?php

namespace App\Enum;

enum PaymentMethodType: int
{
    case CREDIT_CARD = 0;

    case DEBIT_CARD = 1;

    case PIX = 2;
}
