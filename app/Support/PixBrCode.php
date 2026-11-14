<?php

namespace App\Support;

class PixBrCode
{
    public static function payload(
        string $key,
        string $merchantName,
        string $merchantCity,
        float $amount,
        string $txid,
        string $description = 'Pagamento demonstrativo Egg\'s Club',
    ): string {
        $gui = self::tlv('00', 'br.gov.bcb.pix');
        $pixKey = self::tlv('01', $key);
        $desc = self::tlv('02', mb_substr($description, 0, 72));
        $merchantAccount = self::tlv('26', $gui.$pixKey.$desc);

        $name = self::tlv('59', self::ascii($merchantName, 25));
        $city = self::tlv('60', self::ascii($merchantCity, 15));
        $amountField = $amount > 0 ? self::tlv('54', number_format($amount, 2, '.', '')) : '';
        $additional = self::tlv('62', self::tlv('05', self::ascii($txid, 25)));

        $base = '000201010212'
            .$merchantAccount
            .'52040000'
            .'5303986'
            .$amountField
            .'5802BR'
            .$name
            .$city
            .$additional
            .'6304';

        return $base.self::crc16($base);
    }

    private static function tlv(string $id, string $value): string
    {
        return $id.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private static function ascii(string $value, int $max): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $normalized = preg_replace('/[^A-Za-z0-9 ]/', '', $normalized) ?? $normalized;

        return strtoupper(substr(trim($normalized), 0, $max));
    }

    private static function crc16(string $payload): string
    {
        $result = 0xFFFF;
        $length = strlen($payload);
        for ($i = 0; $i < $length; $i++) {
            $result ^= ord($payload[$i]) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                if (($result & 0x8000) !== 0) {
                    $result = ($result << 1) ^ 0x1021;
                } else {
                    $result <<= 1;
                }
                $result &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
    }
}
