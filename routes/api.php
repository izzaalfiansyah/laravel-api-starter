<?php

use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [Controllers\AuthController::class, 'login']);
Route::post('/forgot-password', [Controllers\AuthController::class, 'sendForgotPasswordEmail']);
Route::post('/reset-password', [Controllers\AuthController::class, 'resetPasswordEmail']);
Route::get('/verify/{id}/{hash}', [Controllers\AuthController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [Controllers\AuthController::class, 'profile']);
    Route::get('/logout', [Controllers\AuthController::class, 'logout']);
    Route::get('/verify', [Controllers\AuthController::class, 'sendVerificationEmail']);

    Route::middleware('verified')->group(function () {
        Route::resource('/user', Controllers\UserController::class);
    });
});
