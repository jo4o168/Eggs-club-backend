<?php

use App\Http\Controllers\ProducerSettingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanToggleFeaturedController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanToggleStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    //Contacts
    Route::apiResource('/profiles', ProfileController::class);

    //Producer Settings
    Route::apiResource('/producer-settings', ProducerSettingController::class);

    //Products
    Route::apiResource('/products', ProductController::class);

    //Subscription Plans
    Route::apiResource('/subscription-plans', SubscriptionPlanController::class);
    Route::post('/subscription-plans/{subscription_plan}/toggle-active', SubscriptionPlanToggleStatusController::class);
    Route::post('/subscription-plans/{subscription_plan}/toggle-featured', SubscriptionPlanToggleFeaturedController::class);
});

