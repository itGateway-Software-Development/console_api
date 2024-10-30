<?php

use App\Modules\ServerManagement\ServerTypes\Controllers\Api\V1\ServerTypeController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    Route::post('servers/{serverType}', [ServerTypeController::class, 'updateServer']);
    Route::post('del/multi-servers', [ServerTypeController::class, 'destroyMultiServers']);
    Route::resource('servers', ServerTypeController::class);
});
