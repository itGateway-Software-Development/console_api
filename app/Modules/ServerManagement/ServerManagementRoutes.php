<?php

use App\Modules\ServerManagement\Services\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Modules\ServerManagement\Region\Controllers\Api\V1\RegionController;
use App\Modules\ServerManagement\ServiceCategory\Controllers\Api\V1\ServiceCategoryController;


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function() {
    // region
    Route::post('update-region/{region}', [RegionController::class, 'updateRegion']);
    Route::post('del/multi-regions', [RegionController::class, 'destroyMultiRegions']);
    Route::resource('regions', RegionController::class);

    // service category
    Route::post('update-service-category/{service_category}', [ServiceCategoryController::class, 'updateServiceCategory']);
    Route::post('del/multi-service-categories', [ServiceCategoryController::class, 'destroyMultiServiceCategories']);
    Route::resource('service-categories', ServiceCategoryController::class);

    // services
    Route::post('update-service/{service}', [ServiceController::class, 'updateService']);
    Route::post('del/multi-services', [ServiceController::class, 'destroyMultiServices']);
    Route::resource('services', ServiceController::class);
});
