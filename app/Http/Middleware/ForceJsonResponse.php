<?php
// app/Http/Middleware/ForceJsonResponse.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    // antes de la petición: toda llamada a la API se trata como JSON.
    // así los errores (401, 422, 404) nunca devuelven una redirección HTML.
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}