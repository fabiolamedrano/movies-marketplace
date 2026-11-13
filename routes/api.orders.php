<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('live', function () {
    return response()->json(['service' => 'orders', 'live' => 'is running'], 200);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get("", [OrderController::class, 'index'])->name('orders.index');             // mis compras
    Route::get("seller", [OrderController::class, 'sellerOrders'])->name('orders.seller'); // pedidos de mis películas
    Route::get("{orderId}", [OrderController::class, 'find'])->whereNumber('orderId')->name('orders.find');
    Route::post("", [OrderController::class, 'store'])->name('orders.store');
    Route::patch("{orderId}/status", [OrderController::class, 'updateStatus'])->whereNumber('orderId')->name('orders.status');
    Route::patch("{orderId}/cancel", [OrderController::class, 'cancel'])->whereNumber('orderId')->name('orders.cancel');
});