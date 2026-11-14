<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Resources\Orders\OrderDetailResource;
use App\Http\Services\Order\ConfirmOrderDeliveryService;
use App\Http\Services\Order\ShowOrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderDeliveryController extends Controller
{
    public function __construct(
        private readonly ConfirmOrderDeliveryService $confirmDelivery,
        private readonly ShowOrderService $showOrder,
    ) {
    }

    public function store(Request $request, Order $order): JsonResponse
    {
        $this->confirmDelivery->run($order, $request->user());
        $fresh = $this->showOrder->run($order->fresh(), $request->user());

        return HttpResponse::ok(new OrderDetailResource($fresh));
    }
}
