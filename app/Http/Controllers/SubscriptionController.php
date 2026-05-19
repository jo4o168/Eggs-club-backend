<?php

namespace App\Http\Controllers;

use App\Http\Filters\Filter\DefaultFilter;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\Subscriptions\SubscriptionResource;
use App\Http\Services\Subscription\DeleteSubscriptionService;
use App\Http\Services\Subscription\ListSubscriptionService;
use App\Http\Services\Subscription\ShowSubscriptionService;
use App\Http\Services\Subscription\StoreSubscriptionService;
use App\Http\Services\Subscription\UpdateSubscriptionService;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionService   $listService,
        private readonly StoreSubscriptionService  $storeService,
        private readonly ShowSubscriptionService   $showService,
        private readonly UpdateSubscriptionService $updateService,
        private readonly DeleteSubscriptionService $destroyService,
    )
    {

    }

    public function index(DefaultFilter $filter, Request $request): JsonResponse
    {
        $result = $this->listService->run($filter, $request->user());
        return HttpResponse::ok(SubscriptionResource::collection($result));
    }

    public function store(StoreSubscriptionRequest $request): Response
    {
        $this->storeService->run($request->validated(), $request->user());
        return HttpResponse::noContent();
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $result = $this->showService->run($subscription);
        return HttpResponse::ok($result);
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): Response
    {
        $this->updateService->run($request->validated(), $subscription);
        return HttpResponse::noContent();
    }

    public function destroy(Subscription $subscription): Response
    {
        $this->destroyService->run($subscription);
        return HttpResponse::noContent();
    }
}
