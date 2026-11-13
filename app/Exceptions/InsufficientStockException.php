<?php

namespace App\Exceptions;

use App\Models\Movie;
use Exception;
use Illuminate\Http\JsonResponse;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly Movie $movie,
        public readonly int $requested,
    ) {
        parent::__construct(
            "Stock insuficiente para '{$movie->title}'. Disponible: {$movie->stock}, solicitado: {$requested}."
        );
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}