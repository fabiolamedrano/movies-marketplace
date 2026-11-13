<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MovieController extends Controller
{
    public function __construct(private readonly MovieService $movies) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->movies->list($request->only(['format', 'genre', 'search', 'only_available']))
        );
    }

    public function find(int $movieId): JsonResponse
    {
        return response()->json($this->movies->find($movieId));
    }

    public function store(StoreMovieRequest $request): JsonResponse
    {
        $movie = $this->movies->create($request->user()->id, $request->validated());

        return response()->json($movie, 201);
    }

    public function update(UpdateMovieRequest $request, int $movieId): JsonResponse
    {
        $movie = Movie::findOrFail($movieId); // 404 automatico si no existe

        // la MoviePolice solo deja editar al vendedor dueño (403 si no)
        Gate::authorize('update', $movie);

        return response()->json($this->movies->update($movie, $request->validated()));
    }

    public function destroy(int $movieId): JsonResponse
    {
        $movie = Movie::findOrFail($movieId);
        Gate::authorize('delete', $movie);

        // se hace un soft delete, la pelicula sale del catalogo pero los pedidos que ya la incluian conservan su historial
        $this->movies->delete($movie);

        return response()->json(null, 204);
    }
}