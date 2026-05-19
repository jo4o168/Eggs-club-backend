<?php

namespace App\Http\Services\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProductService
{
    public function run(array $data, Product $product, User $user): void
    {
        abort_unless((int) $product->producer_id === (int) $user->profile->id, 403);

        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            $this->deletePublicImage($product->image_url);
            $data['image_url'] = $this->storeImage($image);
        }

        $allowSubscription = array_key_exists('allow_subscription', $data)
            ? (bool) $data['allow_subscription']
            : (bool) $product->allow_subscription;
        $allowOneTimePurchase = array_key_exists('allow_one_time_purchase', $data)
            ? (bool) $data['allow_one_time_purchase']
            : (bool) $product->allow_one_time_purchase;

        if (!$allowSubscription && !$allowOneTimePurchase) {
            abort(422, 'Selecione ao menos uma modalidade de venda.');
        }

        $data['allow_subscription'] = $allowSubscription;
        $data['allow_one_time_purchase'] = $allowOneTimePurchase;

        if (array_key_exists('one_time_price', $data) && $data['one_time_price'] !== null) {
            $data['price'] = (float) $data['one_time_price'];
        } elseif (array_key_exists('subscription_price', $data) && $data['subscription_price'] !== null) {
            $data['price'] = (float) $data['subscription_price'];
        }

        $product->fill($data);
        $product->save();
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');
        return url(Storage::disk('public')->url($path));
    }

    private function deletePublicImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $publicPrefix = '/storage/';
        $position = strpos($imageUrl, $publicPrefix);

        if ($position === false) {
            return;
        }

        $path = substr($imageUrl, $position + strlen($publicPrefix));
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
