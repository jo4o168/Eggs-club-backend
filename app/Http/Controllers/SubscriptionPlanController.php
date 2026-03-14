<?php

namespace App\Http\Controllers;

use App\Http\Filters\Filter\DefaultFilter;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\SubscriptionPlan\StoreSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\UpdateSubscriptionPlanRequest;
use App\Http\Services\SubscriptionPlan\DeleteSubscriptionPlanService;
use App\Http\Services\SubscriptionPlan\ListSubscriptionPlanService;
use App\Http\Services\SubscriptionPlan\ShowSubscriptionPlanService;
use App\Http\Services\SubscriptionPlan\StoreSubscriptionPlanService;
use App\Http\Services\SubscriptionPlan\UpdateSubscriptionPlanService;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionPlanService   $listService,
        private readonly StoreSubscriptionPlanService  $storeService,
        private readonly ShowSubscriptionPlanService   $showService,
        private readonly UpdateSubscriptionPlanService $updateService,
        private readonly DeleteSubscriptionPlanService $deleteService,
    )
    {

    }

    public function index(DefaultFilter $filter)
    {
        $result = $this->listService->run($filter);
        return HttpResponse::ok($result);
    }

    public function store(StoreSubscriptionPlanRequest $request)
    {
        $this->storeService->run($request->validated());
        return HttpResponse::noContent();
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        $result = $this->showService->run($subscriptionPlan);
        return HttpResponse::ok($result);
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        $this->updateService->run($request->validated(), $subscriptionPlan);
        return HttpResponse::noContent();
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $this->deleteService->run($subscriptionPlan);
        return HttpResponse::noContent();
    }
}
