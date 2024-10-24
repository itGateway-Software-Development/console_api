<?php

use App\Modules\ServerManagement\ServerTypes\Controllers\Api\V1\ServerTypeController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    Route::post('server-type/{serverType}', [ServerTypeController::class, 'updateServerType']);
    Route::post('del/multi-server-types', [ServerTypeController::class, 'destroyMultiServerTypes']);
    Route::resource('server-types', ServerTypeController::class);
});
