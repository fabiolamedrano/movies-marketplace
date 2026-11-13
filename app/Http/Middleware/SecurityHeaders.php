<?php
// app/Http/Middleware/SecurityHeaders.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    // Después de la petición: agrega cabeceras de seguridad a cada respuesta.
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // El navegador no debe adivinar el tipo de contenido (evita ejecutar un JSON como script)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');

        return $response;
    }
}