<?php
// app/Policies/MoviePolicy.php
namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    // la autentificacion es para responder el quien eres y el policy es que puedes hacer en este caso solo el vendedor
    // que publico la pelicula puede editarla o eliminarla.
    public function update(User $user, Movie $movie): bool
    {
        return $user->id === $movie->user_id;
    }

    public function delete(User $user, Movie $movie): bool
    {
        return $user->id === $movie->user_id;
    }
}