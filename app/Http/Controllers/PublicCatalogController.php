<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Profile;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function producers(): JsonResponse
    {
        $producers = Profile::query()
            ->with('producerSetting')
            ->where('role', 1)
            ->get()
            ->map(fn (Profile $producer) => $this->publicProducer($producer));

        return HttpResponse::ok($producers);
    }

    public function producer(string $id): JsonResponse
    {
        $producer = Profile::query()
            ->with('producerSetting')
            ->where('role', 1)
            ->findOrFail($id);

        return HttpResponse::ok($this->publicProducer($producer));
    }

    /**
     * Public producer payload without contact details.
     *
     * @return array{id:int,name:string,producerSetting:?array{farm_name:?string,city:?string,state:?string},producer_setting:?array{farm_name:?string,city:?string,state:?string}}
     */
    private function publicProducer(Profile $producer): array
    {
        $setting = $producer->producerSetting
            ? [
                'farm_name' => $producer->producerSetting->farm_name,
                'city' => $producer->producerSetting->city,
                'state' => $producer->producerSetting->state,
            ]
            : null;

        return [
            'id' => $producer->id,
            'name' => $producer->name,
            'producerSetting' => $setting,
            'producer_setting' => $setting,
        ];
    }

    public function plans(Request $request): JsonResponse
    {
        $plans = SubscriptionPlan::query()
            ->with(['product' => fn ($query) => $query->withAvg('reviews', 'rating')->withCount('reviews')])
            ->where('is_active', true)
            ->when($request->filled('producer_id'), fn ($q) => $q->where('producer_id', (int) $request->input('producer_id')))
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', (int) $request->input('product_id')))
            ->get()
            ->each(function (SubscriptionPlan $plan) {
                if ($plan->product) {
                    $this->applyPublicRating($plan->product);
                }
            });

        return HttpResponse::ok($plans);
    }

    public function products(Request $request): JsonResponse
    {
        $products = Product::query()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('allow_one_time_purchase', true)
                    ->orWhereNotNull('one_time_price');
            })
            ->when($request->filled('producer_id'), fn ($q) => $q->where('producer_id', (int) $request->input('producer_id')))
            ->get()
            ->map(fn (Product $product) => $this->applyPublicRating($product));

        return HttpResponse::ok($products);
    }

    private function applyPublicRating(Product $product): Product
    {
        $product->setAttribute('rating_average', round((float) ($product->reviews_avg_rating ?? 0), 1));
        $product->setAttribute('rating_count', (int) ($product->reviews_count ?? 0));
        $product->makeHidden(['reviews_avg_rating', 'reviews_count']);

        return $product;
    }

    public function productReviews(string $id): JsonResponse
    {
        Product::query()->findOrFail($id);

        $reviews = ProductReview::query()
            ->where('product_id', $id)
            ->with('customer:id,name')
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (ProductReview $review) => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'customer_name' => $review->customer?->name,
                'created_at' => $review->created_at,
            ]);

        return HttpResponse::ok($reviews);
    }
}
