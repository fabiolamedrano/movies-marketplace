<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta de diagnóstico inicial para saber si la API del Marketplace responde
Route::get('/', function (Request $request) {
    return response()->json(['live' => 'Movie Marketplace API is running'], 200);
});

// Módulo de Películas (Catálogo en formato físico: Blu-ray, DVD, VHS)
Route::prefix('movies')->group(function() {
    require __DIR__ . '/api.movies.php';
});

// Módulo de Pedidos (Simulación de compras y reserva de inventario)
Route::prefix('orders')->group(function() {
    require __DIR__ . '/api.orders.php';
});
