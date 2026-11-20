<?php

namespace Tests\Feature;

use App\Enum\CartPurchaseMode;
use App\Enum\PaymentMethodType;
use App\Enum\ProfileRole;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutPaymentAndCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_checkout_does_not_expose_pix_code(): void
    {
        [$customer] = $this->oneTimeKitReadyToBuy();
        $payment = $this->addPayment($customer, PaymentMethodType::CREDIT_CARD, '4242', 'Visa');

        Sanctum::actingAs($customer);
        $checkout = $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Flores, 123 - Campinas/SP - 13010-000',
            'payment_method_id' => $payment->id,
        ])->assertOk();

        $orderId = $checkout->json('data.order_ids.0');
        $this->assertNotEmpty($orderId);

        $order = Order::query()->findOrFail($orderId);
        $this->assertSame(PaymentMethodType::CREDIT_CARD->value, (int) $order->payment_type);
        $this->assertSame('4242', $order->payment_last_four);

        $this->getJson("/api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.payment.type', 'credit_card')
            ->assertJsonPath('data.payment.last_four', '4242')
            ->assertJsonPath('data.payment.card_brand', 'Visa');

        $this->getJson("/api/orders/{$orderId}/pix")
            ->assertStatus(422);
    }

    public function test_pix_checkout_still_returns_demo_qr(): void
    {
        [$customer] = $this->oneTimeKitReadyToBuy();
        $payment = $this->addPayment($customer, PaymentMethodType::PIX);

        Sanctum::actingAs($customer);
        $orderId = $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Flores, 123 - Campinas/SP - 13010-000',
            'payment_method_id' => $payment->id,
        ])->assertOk()->json('data.order_ids.0');

        $this->getJson("/api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.payment.type', 'pix');

        $this->getJson("/api/orders/{$orderId}/pix")
            ->assertOk()
            ->assertJsonPath('data.demo', true);
    }

    public function test_public_kits_list_excludes_subscription_only_even_with_leftover_price(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer-catalog@example.com');

        $oneTime = Product::query()->create([
            'name' => 'Kit compra única',
            'price' => 22.00,
            'one_time_price' => 22.00,
            'allow_one_time_purchase' => true,
            'allow_subscription' => false,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
        ]);

        $subscriptionOnly = Product::query()->create([
            'name' => 'Kit só assinatura',
            'price' => 22.00,
            'one_time_price' => 22.00,
            'allow_one_time_purchase' => false,
            'allow_subscription' => true,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
        ]);

        $listed = collect($this->getJson('/api/public/products?sale=one_time')->assertOk()->json('data'));
        $this->assertTrue($listed->contains(fn ($row) => (int) $row['id'] === $oneTime->id));
        $this->assertFalse($listed->contains(fn ($row) => (int) $row['id'] === $subscriptionOnly->id));
        $this->assertTrue($listed->firstWhere('id', $oneTime->id)['allow_one_time_purchase']);

        $all = collect($this->getJson('/api/public/products')->assertOk()->json('data'));
        $this->assertTrue($all->contains(fn ($row) => (int) $row['id'] === $subscriptionOnly->id));

        $this->getJson("/api/public/products/{$subscriptionOnly->id}")
            ->assertOk()
            ->assertJsonPath('data.allow_one_time_purchase', false)
            ->assertJsonPath('data.name', 'Kit só assinatura');
    }

    public function test_cart_rejects_one_time_add_for_subscription_only_kit(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer-cart@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'buyer-cart@example.com');
        $product = Product::query()->create([
            'name' => 'Kit só plano',
            'price' => 49.90,
            'one_time_price' => 49.90,
            'allow_one_time_purchase' => false,
            'allow_subscription' => true,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
        ]);

        Sanctum::actingAs($customer);
        $this->postJson('/api/customer/cart/items', [
            'product_id' => $product->id,
            'purchase_mode' => CartPurchaseMode::ONE_TIME->value,
            'quantity' => 1,
        ])->assertStatus(422);
    }

    /**
     * @return array{0: User, 1: Product}
     */
    private function oneTimeKitReadyToBuy(): array
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer-pay@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'buyer-pay@example.com');
        $product = Product::query()->create([
            'name' => 'Kit pagamento',
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

        return [$customer, $product];
    }

    private function addPayment(User $customer, PaymentMethodType $type, ?string $lastFour = null, ?string $brand = null): PaymentMethod
    {
        return PaymentMethod::query()->create([
            'type' => $type->value,
            'is_default' => true,
            'gateway_id' => 'demo',
            'customer_id' => $customer->profile->id,
            'card_last_four' => $lastFour,
            'card_brand' => $brand,
            'expiration_month' => 12,
            'expiration_year' => 2099,
        ]);
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
