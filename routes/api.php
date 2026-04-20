<?php

use App\Http\Controllers\API\authController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    //Auth routes
    Route::post('/register', [authController::class, 'register']);
    Route::post('/login', [authController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [authController::class, 'profile']);
        Route::post('/logout', [authController::class, 'logout']);
        // Profile update
        Route::patch('/profileUpdate', [ProfileController::class, 'update']);

        // Task routes
        Route::apiResource('tasks', TaskController::class);
    });
});
