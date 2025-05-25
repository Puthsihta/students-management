<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/signup', [AdminAuthController::class, 'signup']);
    Route::post('/verify', [AdminAuthController::class, 'verifyCode']);
    Route::post('/signin', [AdminAuthController::class, 'signin']);

    Route::middleware('auth:sanctum')->post('/logout', [AdminAuthController::class, 'logout']);
});
Route::apiResource('students', StudentController::class);
