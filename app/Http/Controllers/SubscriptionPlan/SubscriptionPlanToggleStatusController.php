<?php

namespace App\Http\Controllers\SubscriptionPlan;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Services\SubscriptionPlan\ToggleActiveSubscriptionPlanService;
use App\Models\SubscriptionPlan;

class SubscriptionPlanToggleStatusController extends Controller
{
    public function __construct(
        private readonly ToggleActiveSubscriptionPlanService $toggleService
    )
    {

    }

    public function __invoke(SubscriptionPlan $subscriptionPlan)
    {
        $this->toggleService->run($subscriptionPlan);
        return HttpResponse::noContent();
    }
}
