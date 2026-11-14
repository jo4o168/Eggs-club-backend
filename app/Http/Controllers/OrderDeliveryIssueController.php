<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Order\ResolveDeliveryIssueRequest;
use App\Http\Requests\Order\StoreDeliveryIssueRequest;
use App\Http\Resources\Orders\OrderDetailResource;
use App\Http\Services\Order\ResolveDeliveryIssueService;
use App\Http\Services\Order\ShowOrderService;
use App\Http\Services\Order\StoreDeliveryIssueService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderDeliveryIssueController extends Controller
{
    public function __construct(
        private readonly StoreDeliveryIssueService $storeIssue,
        private readonly ResolveDeliveryIssueService $resolveIssue,
        private readonly ShowOrderService $showOrder,
    ) {
    }

    public function store(StoreDeliveryIssueRequest $request, Order $order): JsonResponse
    {
        $this->storeIssue->run($order, $request->validated(), $request->user());
        $fresh = $this->showOrder->run($order->fresh(), $request->user());

        return HttpResponse::ok(new OrderDetailResource($fresh));
    }

    public function update(ResolveDeliveryIssueRequest $request, Order $order): JsonResponse
    {
        $this->resolveIssue->run($order, $request->validated(), $request->user());
        $fresh = $this->showOrder->run($order->fresh(), $request->user());

        return HttpResponse::ok(new OrderDetailResource($fresh));
    }
}
