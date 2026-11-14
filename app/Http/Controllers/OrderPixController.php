<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Services\Order\ShowOrderPixService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderPixController extends Controller
{
    public function __construct(private readonly ShowOrderPixService $showPix)
    {
    }

    public function __invoke(Request $request, Order $order): JsonResponse
    {
        return HttpResponse::ok($this->showPix->run($order, $request->user()));
    }
}
