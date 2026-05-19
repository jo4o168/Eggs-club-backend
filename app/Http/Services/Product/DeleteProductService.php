<?php

namespace App\Http\Services\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DeleteProductService
{
    public function run(Product $product, User $user): void
    {
        abort_unless((int) $product->producer_id === (int) $user->profile->id, 403);
        $this->deletePublicImage($product->image_url);
        $product->delete();
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
