<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Services\Order\IssueDemoNfeService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderNfeController extends Controller
{
    public function __construct(private readonly IssueDemoNfeService $issueNfe)
    {
    }

    public function __invoke(Request $request, Order $order): JsonResponse
    {
        return HttpResponse::ok($this->issueNfe->run($order, $request->user()));
    }
}
