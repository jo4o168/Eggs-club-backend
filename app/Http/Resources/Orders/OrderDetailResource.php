<?php

namespace App\Http\Resources\Orders;

use App\Enum\DeliveryIssueRequest;
use App\Enum\OrderStatus;
use App\Enum\PaymentMethodType;
use App\Http\Services\Order\OrderReplacementAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = strtolower(OrderStatus::tryFrom((int) $this->status)?->name ?? 'PENDING');
        $user = $request->user();
        $issue = $this->relationLoaded('deliveryIssue') ? $this->deliveryIssue : null;
        $hasIssue = $issue !== null;
        $stock = app(OrderReplacementAvailability::class)->assess($this->resource);
        $requested = $issue?->requested_resolution;
        $suggested = $requested === DeliveryIssueRequest::REPLACEMENT->value && $stock['can_replace']
            ? 'replaced'
            : 'refunded';

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $status,
            'total_amount' => (float) $this->total_amount,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->notes,
            'producer_message' => $this->visibleProducerMessage(),
            'producer_id' => (int) $this->producer_id,
            'customer_id' => (int) $this->customer_id,
            'can_confirm_delivery' => (bool) $user?->isClient() && $status === 'shipped',
            'can_review' => (bool) $user?->isClient() && $status === 'delivered',
            'can_report_issue' => (bool) $user?->isClient() && $status === 'delivered' && ! $hasIssue,
            'can_resolve_issue' => (bool) $user?->isProducer() && $hasIssue && $issue->status === 'open',
            'can_replace' => (bool) $user?->isProducer() && $hasIssue && $issue->status === 'open' && $stock['can_replace'],
            'can_issue_nfe' => (bool) $user?->isProducer() && blank($this->nfe_access_key),
            'suggested_resolution' => $hasIssue && $issue->status === 'open' ? $suggested : null,
            'replacement_stock' => $stock['lines'],
            'customer' => $this->customer ? [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
            ] : null,
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'product_id' => $item->product_id,
            ])->values(),
            'reviews' => $this->whenLoaded('reviews', fn () => $this->reviews->map(fn ($review) => [
                'id' => $review->id,
                'product_id' => (int) $review->product_id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
            ])->values()),
            'payment' => $this->paymentSummary(),
            'nfe_access_key' => $this->nfe_access_key,
            'nfe_number' => $this->nfe_number,
            'delivery_issue' => $hasIssue ? [
                'reason' => $issue->reason,
                'requested_resolution' => $issue->requested_resolution,
                'description' => $issue->description,
                'evidence' => collect($issue->evidence ?? [])->map(fn ($item) => [
                    'url' => is_array($item) ? ($item['url'] ?? null) : null,
                    'mime' => is_array($item) ? ($item['mime'] ?? null) : null,
                    'kind' => is_array($item) ? ($item['kind'] ?? 'image') : 'image',
                ])->filter(fn ($item) => filled($item['url']))->values(),
                'status' => $issue->status,
                'resolution_message' => $issue->resolution_message,
                'resolved_at' => $issue->resolved_at,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * @return array{type:string,last_four:?string,card_brand:?string}|null
     */
    private function paymentSummary(): ?array
    {
        $type = PaymentMethodType::slugOrNull($this->payment_type);
        if ($type === null) {
            return null;
        }

        return [
            'type' => $type,
            'last_four' => $this->payment_last_four,
            'card_brand' => $this->payment_brand,
        ];
    }
}
