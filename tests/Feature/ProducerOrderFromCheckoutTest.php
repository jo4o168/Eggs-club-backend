<?php

namespace Tests\Feature;

use App\Enum\CartPurchaseMode;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethodType;
use App\Enum\ProfileRole;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProducerOrderFromCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_one_time_checkout_appears_in_producer_orders_and_stats(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer@example.com');
        $otherProducer = $this->createAccount(ProfileRole::PRODUCER, 'other-producer@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'customer@example.com');

        $product = Product::query()->create([
            'name' => 'Kit 30 ovos caipiras',
            'price' => 36.00,
            'one_time_price' => 36.00,
            'allow_one_time_purchase' => true,
            'allow_subscription' => false,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'stock_quantity' => 20,
            'unit' => 0,
        ]);

        CartItem::query()->create([
            'customer_id' => $customer->profile->id,
            'product_id' => $product->id,
            'quantity' => 2,
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
        $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Galinhas, 10',
            'notes' => 'Entregar de manhã',
            'payment_method_id' => $payment->id,
        ])->assertOk();

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->profile->id,
            'producer_id' => $producer->profile->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $order = Order::query()->firstOrFail();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Sanctum::actingAs($producer);
        $producerList = $this->getJson('/api/orders')->assertOk();
        $producerOrders = $this->ordersFromResponse($producerList->json('data'));

        $this->assertCount(1, $producerOrders);
        $this->assertSame($order->id, $producerOrders[0]['id']);
        $this->assertSame('pending', $producerOrders[0]['status']);
        $this->assertSame('Kit 30 ovos caipiras', $producerOrders[0]['items'][0]['product_name']);
        $this->assertSame($customer->name, $producerOrders[0]['customer']['name']);

        $this->getJson('/api/stats/producer')
            ->assertOk()
            ->assertJsonPath('data.pendingOrdersCount', 1);

        Sanctum::actingAs($customer);
        $customerList = $this->getJson('/api/orders')->assertOk();
        $this->assertCount(1, $this->ordersFromResponse($customerList->json('data')));

        Sanctum::actingAs($otherProducer);
        $otherList = $this->getJson('/api/orders')->assertOk();
        $this->assertCount(0, $this->ordersFromResponse($otherList->json('data')));
    }

    public function test_subscription_checkout_also_creates_visible_producer_order(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer2@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'customer2@example.com');

        $product = Product::query()->create([
            'name' => 'Kit assinatura',
            'price' => 40.00,
            'subscription_price' => 40.00,
            'allow_one_time_purchase' => false,
            'allow_subscription' => true,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'stock_quantity' => 10,
            'unit' => 0,
        ]);

        $plan = SubscriptionPlan::query()->create([
            'name' => 'Semanal',
            'price' => 42.00,
            'eggs_quantity' => 12,
            'frequency' => 0,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
        ]);

        CartItem::query()->create([
            'customer_id' => $customer->profile->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'purchase_mode' => CartPurchaseMode::SUBSCRIPTION->value,
            'subscription_plan_id' => $plan->id,
        ]);

        $payment = PaymentMethod::query()->create([
            'type' => PaymentMethodType::CREDIT_CARD->value,
            'card_last_four' => '4242',
            'card_brand' => 'Visa',
            'is_default' => true,
            'gateway_id' => 'demo',
            'customer_id' => $customer->profile->id,
            'expiration_month' => 1,
            'expiration_year' => 2099,
        ]);

        Sanctum::actingAs($customer);
        $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Galinhas, 10 - Campinas/SP',
            'payment_method_id' => $payment->id,
        ])->assertOk();

        Sanctum::actingAs($producer);
        $producerOrders = $this->ordersFromResponse($this->getJson('/api/orders')->assertOk()->json('data'));

        $this->assertCount(1, $producerOrders);
        $this->assertSame('Kit assinatura', $producerOrders[0]['items'][0]['product_name']);
        $this->assertEquals(42.0, $producerOrders[0]['total_amount']);
    }

    public function test_subscription_checkout_rejects_pix(): void
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer-pix@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'customer-pix@example.com');

        $product = Product::query()->create([
            'name' => 'Kit plano',
            'price' => 40.00,
            'allow_subscription' => true,
            'allow_one_time_purchase' => false,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
        ]);
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Mensal',
            'price' => 40.00,
            'eggs_quantity' => 12,
            'frequency' => 2,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
        ]);
        CartItem::query()->create([
            'customer_id' => $customer->profile->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'purchase_mode' => CartPurchaseMode::SUBSCRIPTION->value,
            'subscription_plan_id' => $plan->id,
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
        $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Galinhas, 10 - Campinas/SP',
            'payment_method_id' => $payment->id,
        ])->assertStatus(422);
    }

    /**
     * @param  mixed  $payload
     * @return array<int, array<string, mixed>>
     */
    private function ordersFromResponse(mixed $payload): array
    {
        if (is_array($payload) && array_is_list($payload)) {
            return $payload;
        }

        if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return [];
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
