<?php

namespace App\Http\Controllers\SubscriptionPlan;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Services\SubscriptionPlan\ToggleFeaturedSubscriptionPlanService;
use App\Models\SubscriptionPlan;

class SubscriptionPlanToggleFeaturedController extends Controller
{
    public function __construct(
        private readonly ToggleFeaturedSubscriptionPlanService $toggleService
    )
    {

    }

    public function __invoke(SubscriptionPlan $subscriptionPlan)
    {
        $this->toggleService->run($subscriptionPlan);
        return HttpResponse::noContent();
    }
}
