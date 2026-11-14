<?php

namespace Tests\Feature;

use App\Enum\CartPurchaseMode;
use App\Enum\PaymentMethodType;
use App\Enum\ProfileRole;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PresentationCheckoutExtrasTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_up_rejects_minor_and_invalid_cpf(): void
    {
        $this->postJson('/api/auth/sign-up', $this->signUpPayload([
            'birth_date' => now()->subYears(17)->toDateString(),
        ]))->assertStatus(422)->assertJsonValidationErrors(['birth_date']);

        $this->postJson('/api/auth/sign-up', $this->signUpPayload([
            'document' => '111.111.111-11',
        ]))->assertStatus(422)->assertJsonValidationErrors(['document']);
    }

    public function test_sign_up_accepts_adult_with_valid_cpf(): void
    {
        $this->postJson('/api/auth/sign-up', $this->signUpPayload())->assertCreated();

        $this->assertDatabaseHas('profiles', [
            'email' => 'cliente@example.com',
            'cpf' => '39053344705',
            'document_type' => 'cpf',
        ]);
    }

    public function test_order_gets_demo_pix_nfe_and_review(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'buyer@example.com');
        $product = Product::query()->create([
            'name' => 'Kit apresentação',
            'price' => 19.90,
            'one_time_price' => 19.90,
            'allow_one_time_purchase' => true,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
        ]);
        CartItem::query()->create([
            'customer_id' => $customer->profile->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'purchase_mode' => CartPurchaseMode::ONE_TIME->value,
        ]);
        $payment = PaymentMethod::query()->create([
            'type' => PaymentMethodType::PIX->value,
            'is_default' => true,
            'gateway_id' => 'demo',
            'customer_id' => $customer->profile->id,
            'expiration_month' => 1,
            'expiration_year' => 2099,
        ]);

        Sanctum::actingAs($customer);
        $checkout = $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Flores, 123 - Campinas/SP - 13010-000',
            'payment_method_id' => $payment->id,
        ])->assertOk();

        $orderId = $checkout->json('data.order_ids.0');
        $this->assertNotEmpty($orderId);

        $pix = $this->getJson("/api/orders/{$orderId}/pix")->assertOk();
        $this->assertTrue($pix->json('data.demo'));
        $this->assertStringContainsString('br.gov.bcb.pix', $pix->json('data.copia_e_cola'));

        $nfe = $this->getJson("/api/orders/{$orderId}/nfe")->assertOk();
        $this->assertTrue($nfe->json('data.demo'));
        $this->assertSame(44, strlen((string) $nfe->json('data.access_key')));

        $this->postJson("/api/orders/{$orderId}/reviews", [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Ovos frescos, chegou rápido.',
        ])->assertOk();

        $reviews = $this->getJson("/api/public/products/{$product->id}/reviews")->assertOk();
        $this->assertSame(5, $reviews->json('data.0.rating'));

        $catalog = $this->getJson('/api/public/products')->assertOk();
        $listed = collect($catalog->json('data'))->firstWhere('id', $product->id);
        $this->assertSame(5.0, (float) $listed['rating_average']);
        $this->assertSame(1, (int) $listed['rating_count']);
        $this->assertArrayNotHasKey('reviews_avg_rating', $listed);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function signUpPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Maria Cliente',
            'email' => 'cliente@example.com',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
            'role' => ProfileRole::CLIENT->value,
            'document_type' => 'cpf',
            'document' => '390.533.447-05',
            'birth_date' => '1998-01-15',
        ], $overrides);
    }

    private function createAccount(ProfileRole $role, string $email): User
    {
        $user = User::query()->create([
            'name' => $role === ProfileRole::PRODUCER ? 'Produtor' : 'Cliente',
            'username' => strstr($email, '@', true),
            'email' => $email,
            'password' => Hash::make('password'),
            'roles' => $role->value,
            'email_verified_at' => now(),
        ]);

        Profile::query()->create([
            'name' => $user->name,
            'role' => $role->value,
            'email' => $email,
            'user_id' => $user->id,
            'document_type' => 'cpf',
            'cpf' => '39053344705',
            'birth_date' => '1998-01-15',
        ]);

        return $user->fresh(['profile']);
    }
}
