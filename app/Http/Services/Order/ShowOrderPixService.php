<?php

namespace App\Http\Services\Order;

use App\Enum\PaymentMethodType;
use App\Models\Order;
use App\Models\User;
use App\Support\PixBrCode;

class ShowOrderPixService
{
    public function __construct(private readonly ShowOrderService $showOrder)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function run(Order $order, User $user): array
    {
        $order = $this->showOrder->run($order, $user);
        abort_unless(
            $order->payment_type !== null
                && PaymentMethodType::tryFrom((int) $order->payment_type) === PaymentMethodType::PIX,
            422,
            'Este pedido não foi pago com Pix.',
        );
        $payload = PixBrCode::payload(
            'eggsclub@demo.com',
            "Egg's Club",
            'Campinas',
            (float) $order->total_amount,
            'PED'.$order->id,
            'Pedido '.$order->order_number,
        );

        return [
            'demo' => true,
            'notice' => 'QR Code de demonstração. Não está atrelado a uma conta bancária real.',
            'beneficiary' => "Egg's Club (demonstração)",
            'city' => 'Campinas',
            'amount' => (float) $order->total_amount,
            'txid' => 'PED'.$order->id,
            'copia_e_cola' => $payload,
            'qr_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data='.rawurlencode($payload),
        ];
    }
}
