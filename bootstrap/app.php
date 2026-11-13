<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // prepend: corre primero, antes de la autenticación, para que los 401 salgan en JSON
        // append: corre al final, sobre la respuesta ya construida
        $middleware->api(
            prepend: [ForceJsonResponse::class],
            append: [SecurityHeaders::class],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
