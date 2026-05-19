<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignOutController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::prefix('auth')->group(function () {
    Route::post('/sign-in', SignInController::class)->middleware('throttle:5,1');
    Route::post('/sign-up', SignUpController::class)->middleware('throttle:3,1');
    Route::post('/sign-out', SignOutController::class)->middleware('auth:sanctum');
    Route::post('/forgot-password', ForgotPasswordController::class)->middleware('throttle:3,1');
    Route::post('/reset-password', ResetPasswordController::class)->middleware('throttle:5,1');
});
