<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\User;
use App\Models\Movie;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class OrderService
{
    public function listForUser(int $userId, int $perPage = 15)
    {
        return Order::with('movies')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $orderId): Order
    {
        return Order::with('movies')->findOrFail($orderId);
    }

    public function create(int $userId, array $items): Order
    {
        $requested = collect($items)
            ->groupBy('movie_id')
            ->map(fn ($group) => (int) $group->sum('quantity'));
        
        // si algo falla se revierte todo
        return DB::transaction(function () use ($userId, $requested) {

            // el lockForUpdate sirve para cuando por ejemplo dos compradores piden la ultima copia a la vez, el segundo espera,
            // y ve el stock ya actualizado y asi se evita la sobreventa.
            $movies = Movie::whereIn('id', $requested->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0.0;
            $pivotData = [];

            foreach ($requested as $movieId => $quantity) {
                /** @var Movie|null $movie */
                $movie = $movies->get($movieId);

                if (! $movie) {
                    throw (new ModelNotFoundException())->setModel(Movie::class, [$movieId]);
                }

                if ($movie->user_id === $userId) {
                    throw ValidationException::withMessages([
                        'items' => "No puedes comprar tu propia publicación: {$movie->title}.",
                    ]);
                }

                if ($movie->stock < $quantity) {
                    throw new InsufficientStockException($movie, $quantity);
                }

                $movie->decrement('stock', $quantity);

                // el total lo calcula el servidor
                $total += round((float) $movie->price * $quantity, 2);

                // se guarda el precio al momento de la compra ya que el vendedor podria cambiarlo despues
                $pivotData[$movie->id] = [
                    'quantity'          => $quantity,
                    'price_at_purchase' => $movie->price,
                ];
            }

            $order = Order::create([
                'user_id'      => $userId,
                'total_amount' => $total,
                'status'       => OrderStatus::Pendiente,
            ]);

            $order->movies()->attach($pivotData);

            return $order->load('movies');
        });
    }

    public function listForSeller(int $sellerId, int $perPage = 15)
    {
        return Order::with('movies')
            ->whereHas('movies', fn ($q) => $q->where('movies.user_id', $sellerId))
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(Order $order, User $actor, OrderStatus $new): Order
    {
        return DB::transaction(function () use ($order, $actor, $new) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // Transiciones válidas
            $validTransition = match ($new) {
                OrderStatus::Enviado    => $order->status === OrderStatus::Pendiente,
                OrderStatus::Completado => $order->status === OrderStatus::Enviado,
                default                 => false,
            };

            if (! $validTransition) {
                throw ValidationException::withMessages([
                    'status' => "No se puede pasar de '{$order->status->value}' a '{$new->value}'.",
                ]);
            }

            // Quién puede hacer cada transición
            $isBuyer  = $order->user_id === $actor->id;
            $isSeller = $order->movies()->where('movies.user_id', $actor->id)->exists();

            $permitted = $new === OrderStatus::Enviado ? $isSeller : $isBuyer;

            if (! $permitted) {
                throw new AuthorizationException('No tienes permiso para cambiar este pedido a ese estado.');
            }

            $order->update(['status' => $new]);

            return $order->load('movies');
        });
    }

    public function cancel(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== OrderStatus::Pendiente) {
                throw ValidationException::withMessages([
                    'status' => 'Solo se pueden cancelar pedidos pendientes.',
                ]);
            }

            foreach ($order->movies as $movie) {
                $movie->increment('stock', $movie->pivot->quantity);
            }

            $order->update(['status' => OrderStatus::Cancelado]);

            return $order->load('movies');
        });
    }
}