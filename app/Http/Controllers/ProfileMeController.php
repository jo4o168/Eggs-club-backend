<?php

namespace App\Http\Controllers;

use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Profile\UpdateProfileAvatarRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Services\Profile\ShowProfileService;
use App\Http\Services\Profile\UpdateProfileAvatarService;
use App\Http\Services\Profile\UpdateProfileService;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ProfileMeController extends Controller
{
    public function __construct(
        private readonly ShowProfileService $showService,
        private readonly UpdateProfileService $updateService,
        private readonly UpdateProfileAvatarService $updateAvatarService,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $profile = $this->resolveAuthenticatedProfile($request);
        $result = $this->showService->run($profile);
        return HttpResponse::ok($result);
    }

    public function update(UpdateProfileRequest $request): Response
    {
        $profile = $this->resolveAuthenticatedProfile($request);
        $this->updateService->run($request->validated(), $profile);
        return HttpResponse::noContent();
    }

    public function updateAvatar(UpdateProfileAvatarRequest $request): JsonResponse
    {
        $profile = $this->resolveAuthenticatedProfile($request);
        $avatarUrl = $this->updateAvatarService->run($request->file('avatar'), $profile);

        return HttpResponse::ok([
            'avatar_url' => $avatarUrl,
        ], 'Avatar updated');
    }

    private function resolveAuthenticatedProfile(Request $request): Profile
    {
        return $request->user()->profile ?? Profile::create([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'role' => is_array($request->user()->roles) ? (int) ($request->user()->roles[0] ?? 0) : (int) $request->user()->roles,
            'user_id' => $request->user()->id,
        ]);
    }
}
