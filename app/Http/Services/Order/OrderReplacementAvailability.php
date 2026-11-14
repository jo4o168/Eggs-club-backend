<?php

namespace App\Http\Services\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderReplacementAvailability
{
    /**
     * @return array{can_replace: bool, lines: list<array{product_id: int|null, product_name: string, needed: int, available: int}>}
     */
    public function assess(Order $order): array
    {
        $order->loadMissing('items.product');
        $lines = [];
        $canReplace = $order->items->isNotEmpty();

        foreach ($order->items as $item) {
            $needed = max(1, (int) $item->quantity);
            $available = (int) ($item->product?->stock_quantity ?? 0);
            $ok = $item->product_id !== null && $available >= $needed;
            if (! $ok) {
                $canReplace = false;
            }
            $lines[] = [
                'product_id' => $item->product_id ? (int) $item->product_id : null,
                'product_name' => (string) $item->product_name,
                'needed' => $needed,
                'available' => $available,
            ];
        }

        return [
            'can_replace' => $canReplace,
            'lines' => $lines,
        ];
    }

    public function decrementOrFail(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('items');
            foreach ($order->items as $item) {
                $needed = max(1, (int) $item->quantity);
                if (! $item->product_id) {
                    throw ValidationException::withMessages([
                        'status' => ['Não há estoque para reenviar este kit. Estorne o valor.'],
                    ]);
                }

                $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();
                if (! $product || (int) $product->stock_quantity < $needed) {
                    throw ValidationException::withMessages([
                        'status' => ['Não há estoque para reenviar este kit. Estorne o valor.'],
                    ]);
                }

                $product->decrement('stock_quantity', $needed);
            }
        });
    }
}
