<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\User\UpdateUserPasswordRequest;
use App\Http\Services\User\UpdateUserPasswordService;
use Illuminate\Http\Response;

class UpdateUserPasswordController extends Controller
{
    public function __construct(private readonly UpdateUserPasswordService $service)
    {
    }

    public function __invoke(UpdateUserPasswordRequest $request): Response
    {
        $this->service->run(
            $request->user(),
            $request->validated('current_password'),
            $request->validated('password')
        );
        return HttpResponse::noContent();
    }
}
