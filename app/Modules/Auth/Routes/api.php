<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\Api\V1\AuthController;
use App\Modules\UserProfile\Controllers\Api\V1\UserProfileController;


Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::post('/v1/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/v1/send-pw-reset-code', [UserProfileController::class, 'sendPwResetCode']);
Route::post('/v1/check-pw-reset-code', [UserProfileController::class, 'checkPwResetCode']);
Route::post('/v1/reset-pw', [UserProfileController::class, 'resetPassword']);
Route::post('/v1/check-auth', [AuthController::class, 'checkAuth'])->middleware('auth:sanctum');

