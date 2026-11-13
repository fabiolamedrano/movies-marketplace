<?php
// app/Policies/OrderPolicy.php
namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    // Lo ve el comprador o un vendedor cuyas películas están en el pedido
    public function view(User $user, Order $order): bool
    {
        // El comprador ve su pedido...
        return $order->user_id === $user->id
            // ...y también el vendedor cuyas películas están dentro
            || $order->movies()->where('movies.user_id', $user->id)->exists();
    }

    // Solo el comprador cancela
    public function cancel(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}