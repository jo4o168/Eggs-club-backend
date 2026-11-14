<?php

namespace Tests\Unit\Support;

use App\Support\PixBrCode;
use PHPUnit\Framework\TestCase;

class PixBrCodeTest extends TestCase
{
    public function test_builds_emv_payload_with_crc(): void
    {
        $payload = PixBrCode::payload(
            'eggsclub@demo.com',
            "Egg's Club",
            'Campinas',
            19.90,
            'ECLUB1',
        );

        $this->assertStringStartsWith('000201', $payload);
        $this->assertStringContainsString('br.gov.bcb.pix', $payload);
        $this->assertStringContainsString('eggsclub@demo.com', $payload);
        $this->assertStringContainsString('5303986', $payload);
        $this->assertStringContainsString('540519.90', $payload);
        $this->assertMatchesRegularExpression('/6304[0-9A-F]{4}$/', $payload);
        $this->assertSame(4, strlen(substr($payload, -4)));
    }
}
