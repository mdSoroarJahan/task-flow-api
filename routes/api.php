<?php

use App\Http\Controllers\API\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    //Auth routes
    Route::post('/register', [authController::class, 'register']);
    Route::post('/login', [authController::class, 'login']);
});
