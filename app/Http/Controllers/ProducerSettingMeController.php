<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\ProducerSetting\UpdateProducerSettingRequest;
use App\Http\Services\ProducerSetting\ShowMyProducerSettingService;
use App\Http\Services\ProducerSetting\UpsertMyProducerSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProducerSettingMeController extends Controller
{
    public function __construct(
        private readonly ShowMyProducerSettingService $showService,
        private readonly UpsertMyProducerSettingService $upsertService,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $result = $this->showService->run($request->user());
        return HttpResponse::ok($result);
    }

    public function update(UpdateProducerSettingRequest $request): Response
    {
        $this->upsertService->run($request->validated(), $request->user());
        return HttpResponse::noContent();
    }
}
