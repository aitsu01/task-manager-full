<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectTaskController;
use App\Http\Controllers\Api\DashboardController;



Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('projects', ProjectController::class);

    Route::apiResource(
    'projects.tasks',
    \App\Http\Controllers\Api\ProjectTaskController::class
);

   Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);




    
});




