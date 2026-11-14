<?php

namespace Tests\Feature;

use App\Enum\CartPurchaseMode;
use App\Enum\DeliveryIssueReason;
use App\Enum\DeliveryIssueRequest;
use App\Enum\DeliveryIssueStatus;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethodType;
use App\Enum\ProfileRole;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AfterSaleFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_confirms_receipt_reviews_and_reports_broken_eggs(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout();
        Sanctum::actingAs($customer);

        $orderId = $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Flores, 123 - Campinas/SP - 13010-000',
            'payment_method_id' => PaymentMethod::query()->where('customer_id', $customer->profile->id)->value('id'),
        ])->assertOk()->json('data.order_ids.0');

        $this->postJson("/api/orders/{$orderId}/confirm-delivery")->assertStatus(422);
        $this->postJson("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'description' => 'Metade dos ovos chegou quebrada.',
        ])->assertStatus(422);

        Storage::fake('public');
        Order::query()->whereKey($orderId)->update(['status' => OrderStatus::SHIPPED->value]);

        $confirmed = $this->postJson("/api/orders/{$orderId}/confirm-delivery")->assertOk();
        $this->assertTrue($confirmed->json('data.can_review'));
        $this->assertTrue($confirmed->json('data.can_report_issue'));
        $this->assertSame('delivered', $confirmed->json('data.status'));

        $this->postJson("/api/orders/{$orderId}/reviews", [
            'product_id' => $product->id,
            'rating' => 2,
            'comment' => 'Chegou, mas vários ovos quebrados.',
        ])->assertOk();

        $photo = UploadedFile::fake()->createWithContent(
            'ovos-quebrados.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='),
        );
        $video = UploadedFile::fake()->create('ovos-quebrados.webm', 240, 'video/webm');

        $this->postJson("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'description' => 'Metade dos ovos chegou quebrada.',
        ])->assertUnprocessable()->assertJsonValidationErrors(['video']);

        $this->post("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'description' => 'Metade dos ovos chegou quebrada.',
            'photos' => [$photo],
        ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors(['video']);

        $opened = $this->post("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'requested_resolution' => DeliveryIssueRequest::REFUND->value,
            'description' => 'Metade dos ovos chegou quebrada.',
            'video' => $video,
            'photos' => [$photo],
        ], ['Accept' => 'application/json'])->assertOk();

        $this->assertSame('video', $opened->json('data.delivery_issue.evidence.0.kind'));
        $this->assertSame('image', $opened->json('data.delivery_issue.evidence.1.kind'));
        $this->assertSame('refund', $opened->json('data.delivery_issue.requested_resolution'));
        $this->assertSame('refunded', $opened->json('data.suggested_resolution'));
        $this->assertNotEmpty($opened->json('data.delivery_issue.evidence.0.url'));

        Sanctum::actingAs($producer);
        $resolved = $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REFUNDED->value,
            'resolution_message' => 'Vamos estornar o valor integral. Não precisa devolver os ovos.',
        ])->assertOk();

        $this->assertSame('refunded', $resolved->json('data.delivery_issue.status'));
        $this->assertFalse($resolved->json('data.can_resolve_issue'));
        $this->assertStringContainsString('estornado', (string) $resolved->json('data.producer_message'));
    }

    public function test_replacement_uses_stock_or_falls_back_to_refund(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout(stock: 2);
        Sanctum::actingAs($customer);
        $orderId = $this->checkoutDelivered($customer, $product);
        Storage::fake('public');

        $this->post("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'requested_resolution' => DeliveryIssueRequest::REPLACEMENT->value,
            'description' => 'Metade dos ovos chegou quebrada.',
            'video' => UploadedFile::fake()->create('ovos.webm', 240, 'video/webm'),
        ], ['Accept' => 'application/json'])->assertOk();

        Sanctum::actingAs($producer);
        $shown = $this->getJson("/api/orders/{$orderId}")->assertOk();
        $this->assertTrue($shown->json('data.can_replace'));
        $this->assertSame('replaced', $shown->json('data.suggested_resolution'));

        $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REPLACED->value,
            'resolution_message' => 'Vamos enviar outro kit. Não precisa devolver os ovos.',
        ])->assertOk();
        $this->assertSame(1, (int) $product->fresh()->stock_quantity);

        [$producerEmpty, $customerEmpty, $emptyStock] = $this->seedCheckout(stock: 0, emailSuffix: 'b');
        Sanctum::actingAs($customerEmpty);
        $emptyOrderId = $this->checkoutDelivered($customerEmpty, $emptyStock);
        $this->post("/api/orders/{$emptyOrderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::SPOILED->value,
            'requested_resolution' => DeliveryIssueRequest::REPLACEMENT->value,
            'description' => 'Os ovos chegaram impróprios.',
            'video' => UploadedFile::fake()->create('estragado.webm', 240, 'video/webm'),
        ], ['Accept' => 'application/json'])->assertOk();

        Sanctum::actingAs($producerEmpty);
        $blocked = $this->getJson("/api/orders/{$emptyOrderId}")->assertOk();
        $this->assertFalse($blocked->json('data.can_replace'));
        $this->assertSame('refunded', $blocked->json('data.suggested_resolution'));

        $this->putJson("/api/orders/{$emptyOrderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REPLACED->value,
            'resolution_message' => 'Vamos enviar outro kit. Não precisa devolver os ovos.',
        ])->assertUnprocessable()->assertJsonValidationErrors(['status']);

        $this->putJson("/api/orders/{$emptyOrderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REFUNDED->value,
            'resolution_message' => 'Não temos estoque para reenvio. Vamos estornar o valor.',
        ])->assertOk();
        $this->assertSame(0, (int) $emptyStock->fresh()->stock_quantity);
    }

    public function test_requested_resolution_is_required_to_open_issue(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout();
        Sanctum::actingAs($customer);
        $orderId = $this->checkoutDelivered($customer, $product);
        Storage::fake('public');

        $this->post("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'description' => 'Metade dos ovos chegou quebrada.',
            'video' => UploadedFile::fake()->create('ovos.webm', 240, 'video/webm'),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['requested_resolution']);
    }

    public function test_producer_can_refund_when_customer_asked_replacement(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout(stock: 4);
        Sanctum::actingAs($customer);
        $orderId = $this->checkoutDelivered($customer, $product);
        $this->openIssue($orderId, DeliveryIssueRequest::REPLACEMENT);

        Sanctum::actingAs($producer);
        $this->getJson("/api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.can_replace', true)
            ->assertJsonPath('data.suggested_resolution', 'replaced');

        $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REFUNDED->value,
            'resolution_message' => 'Vamos estornar o valor. Não precisa devolver os ovos.',
        ])->assertOk()->assertJsonPath('data.delivery_issue.status', 'refunded');

        $this->assertSame(4, (int) $product->fresh()->stock_quantity);
    }

    public function test_producer_can_replace_when_customer_asked_refund(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout(stock: 5);
        Sanctum::actingAs($customer);
        $orderId = $this->checkoutDelivered($customer, $product);
        $this->openIssue($orderId, DeliveryIssueRequest::REFUND);

        Sanctum::actingAs($producer);
        $this->getJson("/api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.can_replace', true)
            ->assertJsonPath('data.suggested_resolution', 'refunded');

        $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REPLACED->value,
            'resolution_message' => 'Vamos enviar outro kit. Não precisa devolver os ovos.',
        ])->assertOk()->assertJsonPath('data.delivery_issue.status', 'replaced');

        $this->assertSame(4, (int) $product->fresh()->stock_quantity);
    }

    public function test_producer_can_reject_open_issue(): void
    {
        [$producer, $customer, $product] = $this->seedCheckout();
        Sanctum::actingAs($customer);
        $orderId = $this->checkoutDelivered($customer, $product);
        $this->openIssue($orderId, DeliveryIssueRequest::REFUND);

        Sanctum::actingAs($producer);
        $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REJECTED->value,
            'resolution_message' => 'O vídeo não mostra o kit deste pedido.',
        ])->assertOk()->assertJsonPath('data.delivery_issue.status', 'rejected');

        $this->putJson("/api/orders/{$orderId}/delivery-issues", [
            'status' => DeliveryIssueStatus::REFUNDED->value,
            'resolution_message' => 'Vamos estornar o valor. Não precisa devolver os ovos.',
        ])->assertStatus(422);
    }

    /**
     * @return array{0: User, 1: User, 2: Product}
     */
    private function seedCheckout(int $stock = 3, string $emailSuffix = ''): array
    {
        $producer = $this->createAccount(ProfileRole::PRODUCER, 'producer'.$emailSuffix.'@example.com');
        $customer = $this->createAccount(ProfileRole::CLIENT, 'buyer'.$emailSuffix.'@example.com');
        $product = Product::query()->create([
            'name' => 'Kit apresentação',
            'price' => 19.90,
            'one_time_price' => 19.90,
            'allow_one_time_purchase' => true,
            'is_active' => true,
            'producer_id' => $producer->profile->id,
            'unit' => 0,
            'stock_quantity' => $stock,
        ]);
        CartItem::query()->create([
            'customer_id' => $customer->profile->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'purchase_mode' => CartPurchaseMode::ONE_TIME->value,
        ]);
        PaymentMethod::query()->create([
            'type' => PaymentMethodType::PIX->value,
            'is_default' => true,
            'gateway_id' => 'demo',
            'customer_id' => $customer->profile->id,
            'expiration_month' => 1,
            'expiration_year' => 2099,
        ]);

        return [$producer, $customer, $product];
    }

    private function checkoutDelivered(User $customer, Product $product): int
    {
        $orderId = $this->postJson('/api/customer/checkout', [
            'delivery_address' => 'Rua das Flores, 123 - Campinas/SP - 13010-000',
            'payment_method_id' => PaymentMethod::query()->where('customer_id', $customer->profile->id)->value('id'),
        ])->assertOk()->json('data.order_ids.0');
        Order::query()->whereKey($orderId)->update(['status' => OrderStatus::SHIPPED->value]);
        $this->postJson("/api/orders/{$orderId}/confirm-delivery")->assertOk();

        return (int) $orderId;
    }

    private function openIssue(int $orderId, DeliveryIssueRequest $request): void
    {
        Storage::fake('public');
        $this->post("/api/orders/{$orderId}/delivery-issues", [
            'reason' => DeliveryIssueReason::BROKEN->value,
            'requested_resolution' => $request->value,
            'description' => 'Metade dos ovos chegou quebrada.',
            'video' => UploadedFile::fake()->create('ovos.webm', 240, 'video/webm'),
        ], ['Accept' => 'application/json'])->assertOk();
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
