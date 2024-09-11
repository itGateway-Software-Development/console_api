<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::post('/v1/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/v1/send-pw-reset-code', [UserController::class, 'sendPwResetCode']);
Route::post('/v1/check-pw-reset-code', [UserController::class, 'checkPwResetCode']);
Route::post('/v1/reset-pw', [Usercontroller::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    // user setting
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});
