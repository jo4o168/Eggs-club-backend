<?php

namespace App\Http\Services\Order;

use App\Enum\DeliveryIssueStatus;
use App\Models\Order;
use App\Models\OrderDeliveryIssue;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ResolveDeliveryIssueService
{
    public function __construct(
        private readonly OrderReplacementAvailability $replacementStock,
    ) {
    }

    public function run(Order $order, array $data, User $user): OrderDeliveryIssue
    {
        $user->loadMissing('profile');
        abort_unless($user->isProducer(), 403);
        abort_unless(
            Order::query()->whereKey($order->id)->forProducer((int) $user->profile->id)->exists(),
            403
        );

        $issue = $order->deliveryIssue;
        abort_unless($issue instanceof OrderDeliveryIssue, 422, 'Este pedido não tem chamado de pós-compra.');
        abort_unless(
            $issue->status === DeliveryIssueStatus::OPEN->value,
            422,
            'Este chamado já foi resolvido.'
        );

        $status = DeliveryIssueStatus::from((string) $data['status']);
        if ($status === DeliveryIssueStatus::OPEN) {
            throw ValidationException::withMessages([
                'status' => ['Informe se o valor será estornado, se haverá reenvio ou se o chamado não procede.'],
            ]);
        }

        if ($status === DeliveryIssueStatus::REPLACED) {
            $this->replacementStock->decrementOrFail($order);
        }

        $message = trim((string) $data['resolution_message']);
        $issue->fill([
            'status' => $status->value,
            'resolution_message' => $message,
            'resolved_at' => now(),
        ]);
        $issue->save();

        $orderNotice = match ($status) {
            DeliveryIssueStatus::REFUNDED => 'Pós-compra: o valor será estornado. Ovos não retornam ao produtor.',
            DeliveryIssueStatus::REPLACED => 'Pós-compra: um novo kit será enviado. Ovos recebidos não retornam ao produtor.',
            DeliveryIssueStatus::REJECTED => 'Pós-compra: o chamado não procedeu. '.$message,
            default => $message,
        };
        $order->update(['producer_message' => $orderNotice]);

        return $issue;
    }
}
