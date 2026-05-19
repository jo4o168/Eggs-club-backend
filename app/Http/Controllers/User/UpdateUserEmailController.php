<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\User\UpdateUserEmailRequest;
use App\Http\Services\User\UpdateUserEmailService;
use Illuminate\Http\Response;

class UpdateUserEmailController extends Controller
{
    public function __construct(private readonly UpdateUserEmailService $service)
    {
    }

    public function __invoke(UpdateUserEmailRequest $request): Response
    {
        $this->service->run($request->user(), $request->validated('email'));
        return HttpResponse::noContent();
    }
}
