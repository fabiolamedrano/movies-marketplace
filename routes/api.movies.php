<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('live', function () {
    return response()->json(['service' => 'movies', 'live' => 'is running'], 200);
});

// leer el catalogo es publico
Route::get("", [MovieController::class, 'index'])->name('movies.index');
Route::get("{movieId}", [MovieController::class, 'find'])->whereNumber('movieId')->name('movies.find');

// escribir exige token, sin token  da 401
Route::middleware('auth:sanctum')->group(function () {
    Route::post("", [MovieController::class, 'store'])->name('movies.store');
    Route::put("{movieId}", [MovieController::class, 'update'])->whereNumber('movieId')->name('movies.update');
    Route::delete("{movieId}", [MovieController::class, 'destroy'])->whereNumber('movieId')->name('movies.destroy');
});