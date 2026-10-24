<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Endpoint rápido para verificar el estado de salud del servicio de pedidos
Route::get('live', function () {
    return response()->json(['service' => 'orders', 'live' => 'is running'], 200);
});

// Rutas iniciales para el procesamiento de transacciones
Route::get("", [OrderController::class, 'index'])->name('orders.index');       // Historial de compras
Route::get("{orderId}", [OrderController::class, 'find'])->name('orders.find'); // Ver el estado de un pedido específico
Route::post("", [OrderController::class, 'store'])->name('orders.store');       // Generar una nueva compra de películas
