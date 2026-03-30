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
use App\Http\Controllers\Api\UserController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | PROJECTS
    |--------------------------------------------------------------------------
    */

    Route::apiResource('projects', ProjectController::class);

    /*
    |--------------------------------------------------------------------------
    | PROJECT TASKS (Nested)
    |--------------------------------------------------------------------------
    */

    Route::apiResource('projects.tasks', ProjectTaskController::class);

    /*
    |--------------------------------------------------------------------------
    | MEMBERS (Nested clean version)
    |--------------------------------------------------------------------------
    */

    Route::get('/projects/{project}/members', [ProjectMemberController::class, 'index']);
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store']);
    Route::patch('/projects/{project}/members/{user}', [ProjectMemberController::class, 'update']);
    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD & TASKS
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/my-tasks', [MyTaskController::class, 'index']);
    Route::apiResource('tasks', TaskController::class);

    /*
    |--------------------------------------------------------------------------
    | ADMIN USERS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::patch('/admin/users/{user}/approve', [AdminUserController::class, 'approve']);
    Route::patch('/admin/users/{user}/reject', [AdminUserController::class, 'reject']);
    Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | USER PROFILE
    |--------------------------------------------------------------------------
    */

    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

});





