<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Cart\CheckoutCartRequest;
use App\Http\Services\Cart\CheckoutCartService;
use Illuminate\Http\JsonResponse;

class CustomerCheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutCartService $checkoutCart,
    ) {}

    public function store(CheckoutCartRequest $request): JsonResponse
    {
        $result = $this->checkoutCart->run($request->validated(), $request->user());

        return HttpResponse::ok($result);
    }
}
