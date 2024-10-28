<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ServerManagement\OperationSystems\Controllers\Api\V1\OperationSystemController;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    Route::post('operation-system/{operationSystem}', [OperationSystemController::class, 'updateOperationSystem']);
    Route::post('del/multi-operation-systems', [OperationSystemController::class, 'destroyMultiOperationSystems']);
    Route::resource('operation-systems', OperationSystemController::class);
});
