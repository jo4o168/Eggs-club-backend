<?php

namespace App\Http\Services\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

class IssueDemoNfeService
{
    public function __construct(private readonly ShowOrderService $showOrder)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function run(Order $order, User $user): array
    {
        $order = $this->showOrder->run($order, $user)->loadMissing(['items', 'customer', 'producer.producerSetting']);

        if (! $order->nfe_access_key) {
            $order->nfe_number = $order->id + 1000;
            $order->nfe_issued_at = now();
            $order->nfe_access_key = $this->accessKey($order);
            $order->save();
        }

        $issuedAt = $order->nfe_issued_at instanceof Carbon
            ? $order->nfe_issued_at
            : Carbon::parse($order->nfe_issued_at);

        $items = $order->items->map(fn ($item) => [
            'name' => $item->product_name,
            'ncm' => '04072100',
            'cfop' => '5102',
            'quantity' => (int) $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'total' => (float) $item->quantity * (float) $item->unit_price,
        ])->values();

        return [
            'demo' => true,
            'notice' => 'NF-e demonstrativa, sem valor fiscal e sem transmissão à SEFAZ.',
            'model' => '55',
            'series' => 1,
            'number' => (int) $order->nfe_number,
            'issued_at' => $issuedAt->toIso8601String(),
            'access_key' => $order->nfe_access_key,
            'nature' => 'Venda de mercadoria',
            'issuer' => [
                'name' => $order->producer?->producerSetting?->farm_name ?: ($order->producer?->name ?? "Egg's Club"),
                'document' => '11444777000161',
                'ie' => '123456789',
                'address' => 'Plataforma Egg\'s Club — documento de apresentação',
            ],
            'recipient' => [
                'name' => $order->customer?->name,
                'document' => $order->customer?->cpf ?: $order->customer?->cnpj,
                'address' => $order->delivery_address,
            ],
            'items' => $items,
            'totals' => [
                'products' => (float) $order->total_amount,
                'icms' => 0,
                'invoice' => (float) $order->total_amount,
            ],
        ];
    }

    private function accessKey(Order $order): string
    {
        $uf = '35';
        $ym = ($order->nfe_issued_at ?? now())->format('ym');
        $cnpj = '11444777000161';
        $mod = '55';
        $serie = '001';
        $number = str_pad((string) $order->nfe_number, 9, '0', STR_PAD_LEFT);
        $tpEmis = '1';
        $cnf = str_pad((string) (($order->id * 7919) % 100000000), 8, '0', STR_PAD_LEFT);
        $base = $uf.$ym.$cnpj.$mod.$serie.$number.$tpEmis.$cnf;
        $dv = $this->mod11($base);

        return $base.$dv;
    }

    private function mod11(string $digits): int
    {
        $weight = 2;
        $sum = 0;
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $sum += (int) $digits[$i] * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }
        $mod = $sum % 11;

        return $mod < 2 ? 0 : 11 - $mod;
    }
}
