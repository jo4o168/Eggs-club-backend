<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\ProducerSetting\StoreProducerSettingRequest;
use App\Http\Requests\ProducerSetting\UpdateProducerSettingRequest;
use App\Http\Services\ProducerSetting\ShowProducerSettingService;
use App\Http\Services\ProducerSetting\StoreProducerSettingService;
use App\Http\Services\ProducerSetting\UpdateProducerSettingService;
use App\Models\ProducerSetting;

class ProducerSettingController extends Controller
{
    public function __construct(
        private readonly StoreProducerSettingService  $storeService,
        private readonly ShowProducerSettingService   $showService,
        private readonly UpdateProducerSettingService $updateService,
    )
    {

    }

    public function store(StoreProducerSettingRequest $request)
    {
        $this->storeService->run($request->validated());
        return HttpResponse::noContent();
    }

    public function show(ProducerSetting $producerSetting)
    {
        $result = $this->showService->run($producerSetting);
        return HttpResponse::ok($result);
    }

    public function update(UpdateProducerSettingRequest $request, ProducerSetting $producerSetting)
    {
        $this->updateService->run($request->validated(), $producerSetting);
        return HttpResponse::noContent();
    }
}
