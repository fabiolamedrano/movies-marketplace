<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MovieService
{
    /**
     * Catálogo paginado con filtros opcionales: format, genre, search, only_available.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Movie::query()
            ->when($filters['format'] ?? null, fn ($q, $v) => $q->ofFormat($v))
            ->when($filters['genre'] ?? null, fn ($q, $v) => $q->where('genre', $v))
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($filters['only_available'] ?? false, fn ($q) => $q->available())
            ->when($filters['search'] ?? null, function ($q, $v) {
                $escaped = addcslashes($v, '%_\\');
                $q->where('title', 'like', "%{$escaped}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Movie
    {
        return Movie::with('seller:id,name,seller_type')->findOrFail($id);
    }

    public function create(int $sellerId, array $data): Movie
    {
        $movie = new Movie($data);
        $movie->user_id = $sellerId;
        $movie->save();

        return $movie;
    }

    public function update(Movie $movie, array $data): Movie
    {
        $movie->update($data);

        return $movie->refresh();
    }

    public function delete(Movie $movie): void
    {
        $movie->delete();
    }
}