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

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/user', Controllers\UserController::class);
    Route::get('/profile', [Controllers\AuthController::class, 'profile']);
    Route::get('/logout', [Controllers\AuthController::class, 'logout']);
});

Route::post('/login', [Controllers\AuthController::class, 'login']);
