<?php

namespace Tests\Feature;

use App\Enum\ProfileRole;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyAccountNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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

    public function test_sign_up_sends_branded_verification_mail(): void
    {
        Notification::fake();

        $this->postJson('/api/auth/sign-up', [
            'name' => 'Cliente Novo',
            'email' => 'cliente.novo@example.com',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
            'role' => ProfileRole::CLIENT->value,
            'document_type' => 'cpf',
            'document' => '390.533.447-05',
            'birth_date' => '1998-01-15',
        ])->assertCreated();

        $user = User::query()->where('email', 'cliente.novo@example.com')->first();
        $this->assertNotNull($user);
        Notification::assertSentTo($user, VerifyAccountNotification::class);
    }

    public function test_forgot_password_sends_branded_reset_mail(): void
    {
        Notification::fake();

        $user = User::query()->create([
            'name' => 'Cliente Reset',
            'username' => 'cliente.reset@example.com',
            'email' => 'cliente.reset@example.com',
            'password' => Hash::make('Senha@123'),
            'roles' => 0,
            'email_verified_at' => now(),
            'active' => true,
        ]);

        $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ])->assertOk();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_order_show_requires_authentication(): void
    {
        $this->getJson('/api/orders/1')->assertUnauthorized();
    }
}
