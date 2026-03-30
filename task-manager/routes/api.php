<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectTaskController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\MyTaskController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('projects', ProjectController::class);

    Route::apiResource('projects.tasks', ProjectTaskController::class);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/my-tasks', [MyTaskController::class, 'index']);

    Route::apiResource('tasks', TaskController::class);
    

    // ADMIN ROUTES
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::patch('/admin/users/{user}/approve', [AdminUserController::class, 'approve']);
    Route::patch('/admin/users/{user}/reject', [AdminUserController::class, 'reject']);
    Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy']);


   

Route::prefix('projects/{project}')->group(function () {

    Route::get('/members', [ProjectMemberController::class, 'index']);
    Route::post('/members', [ProjectMemberController::class, 'store']);
    Route::patch('/members/{user}', [ProjectMemberController::class, 'update']);
    Route::delete('/members/{user}', [ProjectMemberController::class, 'destroy']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/projects/{project}/members', [ProjectMemberController::class, 'index']);

    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store']);

    Route::patch('/projects/{project}/members/{user}', [ProjectMemberController::class, 'update']);

    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy']);

});




});





});


