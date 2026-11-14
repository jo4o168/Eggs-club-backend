<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Review\StoreProductReviewRequest;
use App\Http\Services\Review\StoreProductReviewService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderReviewController extends Controller
{
    public function __construct(private readonly StoreProductReviewService $storeReview)
    {
    }

    public function store(StoreProductReviewRequest $request, Order $order): JsonResponse
    {
        $review = $this->storeReview->run($order, $request->validated(), $request->user());

        return HttpResponse::ok([
            'id' => $review->id,
            'rating' => (int) $review->rating,
            'comment' => $review->comment,
            'product_id' => (int) $review->product_id,
            'order_id' => (int) $review->order_id,
        ]);
    }
}
