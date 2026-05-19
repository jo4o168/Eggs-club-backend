<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_returns_envelope(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['notice'], 'message']);
    }

    public function test_order_show_requires_authentication(): void
    {
        $this->getJson('/api/orders/1')->assertUnauthorized();
    }
}
