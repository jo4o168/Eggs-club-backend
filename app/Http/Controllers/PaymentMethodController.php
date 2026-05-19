<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Resources\PaymentMethods\PaymentMethodResource;
use App\Http\Services\PaymentMethod\DeletePaymentMethodService;
use App\Http\Services\PaymentMethod\ListPaymentMethodService;
use App\Http\Services\PaymentMethod\SetDefaultPaymentMethodService;
use App\Http\Services\PaymentMethod\StorePaymentMethodService;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentMethodController extends Controller
{
    public function __construct(
        private readonly ListPaymentMethodService $listService,
        private readonly StorePaymentMethodService $storeService,
        private readonly DeletePaymentMethodService $deleteService,
        private readonly SetDefaultPaymentMethodService $setDefaultService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $result = $this->listService->run($request->user());
        return HttpResponse::ok(PaymentMethodResource::collection($result));
    }

    public function store(StorePaymentMethodRequest $request): Response
    {
        $this->storeService->run($request->validated(), $request->user());
        return HttpResponse::noContent();
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): Response
    {
        $this->deleteService->run($paymentMethod, $request->user());
        return HttpResponse::noContent();
    }

    public function setDefault(Request $request, PaymentMethod $paymentMethod): Response
    {
        $this->setDefaultService->run($paymentMethod, $request->user());
        return HttpResponse::noContent();
    }
}
