<?php

use App\Http\Controllers\Customer\CustomerCartController;
use App\Http\Controllers\Customer\CustomerCheckoutController;
use App\Http\Controllers\ProducerSettingController;
use App\Http\Controllers\ProducerSettingMeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\ProfileMeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Stats\CustomerStatsController;
use App\Http\Controllers\Stats\ProducerStatsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanToggleFeaturedController;
use App\Http\Controllers\SubscriptionPlan\SubscriptionPlanToggleStatusController;
use App\Http\Controllers\User\UpdateUserEmailController;
use App\Http\Controllers\User\UpdateUserPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/public/producers', [PublicCatalogController::class, 'producers']);
Route::get('/public/producers/{id}', [PublicCatalogController::class, 'producer']);
Route::get('/public/subscription-plans', [PublicCatalogController::class, 'plans']);
Route::get('/public/products', [PublicCatalogController::class, 'products']);

Route::middleware('auth:sanctum')->group(function () {
    //Contacts
    Route::get('/profiles/me', [ProfileMeController::class, 'show']);
    Route::put('/profiles/me', [ProfileMeController::class, 'update']);
    Route::post('/profiles/me/avatar', [ProfileMeController::class, 'updateAvatar']);

    //Producer Settings
    Route::get('/producer-settings/me', [ProducerSettingMeController::class, 'show']);
    Route::put('/producer-settings/me', [ProducerSettingMeController::class, 'update']);
    Route::apiResource('/producer-settings', ProducerSettingController::class)->only(['store', 'show', 'update']);

    //Products
    Route::apiResource('/products', ProductController::class);

    //Subscription Plans
    Route::apiResource('/subscription-plans', SubscriptionPlanController::class);
    Route::post('/subscription-plans/{subscription_plan}/toggle-active', SubscriptionPlanToggleStatusController::class);
    Route::post('/subscription-plans/{subscription_plan}/toggle-featured', SubscriptionPlanToggleFeaturedController::class);

    //Subscription
    Route::apiResource('/subscriptions', SubscriptionController::class);

    //Customer cart & checkout (client role)
    Route::get('/customer/cart', [CustomerCartController::class, 'index']);
    Route::post('/customer/cart/items', [CustomerCartController::class, 'store']);
    Route::put('/customer/cart/items/{cartItem}', [CustomerCartController::class, 'update']);
    Route::delete('/customer/cart/items/{cartItem}', [CustomerCartController::class, 'destroyItem']);
    Route::delete('/customer/cart', [CustomerCartController::class, 'destroy']);
    Route::post('/customer/checkout', [CustomerCheckoutController::class, 'store']);

    //Orders
    Route::apiResource('/orders', OrderController::class)->only(['index', 'store', 'show', 'update']);

    //Payment Methods
    Route::apiResource('/payment-methods', PaymentMethodController::class)->only(['index', 'store', 'destroy']);
    Route::put('/payment-methods/{paymentMethod}/default', [PaymentMethodController::class, 'setDefault']);

    //Stats
    Route::get('/stats/producer', ProducerStatsController::class);
    Route::get('/stats/customer', CustomerStatsController::class);

    //User Security
    Route::put('/user/password', UpdateUserPasswordController::class);
    Route::put('/user/email', UpdateUserEmailController::class);
});

