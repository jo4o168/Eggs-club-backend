<?php

namespace Tests\Unit\Support;

use App\Support\BrazilianDocument;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BrazilianDocumentTest extends TestCase
{
    public function test_accepts_valid_cpf_and_rejects_invalid_ones(): void
    {
        $this->assertTrue(BrazilianDocument::isValidCpf('390.533.447-05'));
        $this->assertTrue(BrazilianDocument::isValidCpf('52998224725'));
        $this->assertFalse(BrazilianDocument::isValidCpf('111.111.111-11'));
        $this->assertFalse(BrazilianDocument::isValidCpf('12345678900'));
        $this->assertFalse(BrazilianDocument::isValidCpf('3905334470'));
    }

    public function test_accepts_valid_cnpj_and_rejects_invalid_ones(): void
    {
        $this->assertTrue(BrazilianDocument::isValidCnpj('11.444.777/0001-61'));
        $this->assertFalse(BrazilianDocument::isValidCnpj('00.000.000/0000-00'));
        $this->assertFalse(BrazilianDocument::isValidCnpj('1144477700016'));
    }

    public function test_requires_eighteen_years(): void
    {
        $today = Carbon::parse('2026-11-14');

        $this->assertTrue(BrazilianDocument::isAdult('2008-11-14', $today));
        $this->assertFalse(BrazilianDocument::isAdult('2008-11-15', $today));
        $this->assertFalse(BrazilianDocument::isAdult('not-a-date', $today));
    }
}
