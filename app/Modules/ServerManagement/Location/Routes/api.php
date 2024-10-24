<?php

use App\Modules\ServerManagement\Location\Controllers\Api\V1\LocationController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    Route::post('location/{location}', [LocationController::class, 'updateLocation']);
    Route::post('del/multi-locations', [LocationController::class, 'destroyMultiLocations']);
    Route::resource('locations', LocationController::class);
});
