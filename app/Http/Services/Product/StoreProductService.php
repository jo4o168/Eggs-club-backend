<?php

namespace App\Http\Services\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreProductService
{
    public function run(array $data, User $user): void
    {
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            $data['image_url'] = $this->storeImage($image);
        }

        $data['allow_subscription'] = (bool) ($data['allow_subscription'] ?? true);
        $data['allow_one_time_purchase'] = (bool) ($data['allow_one_time_purchase'] ?? true);

        if (!$data['allow_subscription'] && !$data['allow_one_time_purchase']) {
            abort(422, 'Selecione ao menos uma modalidade de venda.');
        }

        $data['subscription_price'] = $data['subscription_price'] ?? null;
        $data['one_time_price'] = $data['one_time_price'] ?? null;
        $data['price'] = (float) ($data['one_time_price'] ?? $data['subscription_price'] ?? $data['price']);

        $data['producer_id'] = $user->profile->id;
        Product::create($data);
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');
        return url(Storage::disk('public')->url($path));
    }
}
