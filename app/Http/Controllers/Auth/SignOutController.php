<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SignOutController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $request->user()?->currentAccessToken()?->delete();
        return HttpResponse::noContent();
    }
}
