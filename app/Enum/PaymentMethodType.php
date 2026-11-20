<?php

namespace App\Enum;

enum PaymentMethodType: int
{
    case CREDIT_CARD = 0;

    case DEBIT_CARD = 1;

    case PIX = 2;

    public function slug(): string
    {
        return strtolower($this->name);
    }

    public static function slugOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom((int) $value)?->slug();
    }
}
