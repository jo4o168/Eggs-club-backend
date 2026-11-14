<?php

namespace App\Support;

use Carbon\Carbon;

class BrazilianDocument
{
    public static function digits(mixed $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }

    public static function isValidCpf(mixed $value): bool
    {
        $digits = self::digits($value);
        if (strlen($digits) !== 11 || preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        return self::cpfCheckDigit($digits, 9) === (int) $digits[9]
            && self::cpfCheckDigit($digits, 10) === (int) $digits[10];
    }

    public static function isValidCnpj(mixed $value): bool
    {
        $digits = self::digits($value);
        if (strlen($digits) !== 14 || preg_match('/^(\d)\1{13}$/', $digits)) {
            return false;
        }

        return self::cnpjCheckDigit($digits, 12) === (int) $digits[12]
            && self::cnpjCheckDigit($digits, 13) === (int) $digits[13];
    }

    public static function isAdult(?string $birthDate, ?Carbon $today = null): bool
    {
        if (! is_string($birthDate) || trim($birthDate) === '') {
            return false;
        }

        try {
            $born = Carbon::parse($birthDate)->startOfDay();
        } catch (\Throwable) {
            return false;
        }

        $today ??= Carbon::now();

        return $born->lte($today) && $born->age >= 18;
    }

    private static function cpfCheckDigit(string $digits, int $length): int
    {
        $sum = 0;
        $weight = $length + 1;
        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * ($weight - $i);
        }
        $mod = ($sum * 10) % 11;

        return $mod === 10 ? 0 : $mod;
    }

    private static function cnpjCheckDigit(string $digits, int $length): int
    {
        $weights = $length === 12
            ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
            : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }
        $mod = $sum % 11;

        return $mod < 2 ? 0 : 11 - $mod;
    }
}
