<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

// Endpoint rápido para verificar el estado de salud de este servicio aislado
Route::get('live', function () {
    return response()->json(['service' => 'movies', 'live' => 'is running'], 200);
});

// Rutas básicas del CRUD que se conectarán a las funciones de nuestro controlador
Route::get("", [MovieController::class, 'index'])->name('movies.index');       // Ver catálogo completo
Route::get("{movieId}", [MovieController::class, 'find'])->name('movies.find'); // Buscar película por ID
Route::post("", [MovieController::class, 'store'])->name('movies.store');       // Subir una nueva película (Vendedores)
Route::put("{movieId}", [MovieController::class, 'update'])->name('movies.update'); // Editar precio o stock físico
Route::delete("{movieId}", [MovieController::class, 'destroy'])->name('movies.destroy'); // Eliminar publicación
