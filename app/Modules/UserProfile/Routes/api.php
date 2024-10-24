<?php

use Illuminate\Support\Facades\Route;
use App\Modules\UserProfile\Controllers\Api\V1\UserProfileController;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    // user setting
    Route::post('/update-profile', [UserProfileController::class, 'updateProfile']);
    Route::post('/change-password', [UserProfileController::class, 'changePassword']);
});
