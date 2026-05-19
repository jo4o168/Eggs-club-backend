<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Resources\Stats\CustomerStatsResource;
use App\Http\Services\Stats\GetCustomerStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerStatsController extends Controller
{
    public function __construct(private readonly GetCustomerStatsService $service)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return HttpResponse::ok(new CustomerStatsResource($this->service->run($request->user())));
    }
}
