<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\VehicleImageController;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:3,1')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::apiResource('vehicles', VehicleController::class);

        Route::post('/vehicles/{vehicleId}/images', [VehicleImageController::class, 'store']);
        Route::patch('/vehicles/{vehicleId}/images/{imageId}/cover', [VehicleImageController::class, 'setCover']);
        Route::delete('/vehicles/{vehicleId}/images/{imageId}', [VehicleImageController::class, 'destroy']);
    });
});
