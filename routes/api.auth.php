<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// throttle: maximo 5 intentos por minuto (frena fuerza bruta)
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});