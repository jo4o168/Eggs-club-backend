<?php

namespace App\Http\Controllers;

use App\Http\Filters\Filter\DefaultFilter;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Profile\StoreProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Services\Profile\DeleteProfileService;
use App\Http\Services\Profile\ListProfileService;
use App\Http\Services\Profile\ShowProfileService;
use App\Http\Services\Profile\StoreProfileService;
use App\Http\Services\Profile\UpdateProfileService;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ListProfileService   $listService,
        private readonly ShowProfileService   $showService,
        private readonly StoreProfileService  $storeService,
        private readonly UpdateProfileService $updateService,
        private readonly DeleteProfileService $deleteService
    )
    {

    }

    public function index(DefaultFilter $filter): JsonResponse
    {
        $result = $this->listService->run($filter);
        return HttpResponse::ok($result);
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $this->storeService->run($request->validated());
        return HttpResponse::created([]);
    }

    public function show(Profile $profile): JsonResponse
    {
        $result = $this->showService->run($profile);
        return HttpResponse::ok($result);
    }

    public function update(UpdateProfileRequest $request, Profile $profile): Response
    {
        $this->updateService->run($request->validated(), $profile);
        return HttpResponse::noContent();
    }

    public function destroy(Profile $profile): Response
    {
        $this->deleteService->run($profile);
        return HttpResponse::noContent();
    }
}
