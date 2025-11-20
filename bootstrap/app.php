<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Register middleware aliases correctly
        $middleware->alias(['auth.token', \Presentation\Http\Middleware\AuthToken::class]);
        $middleware->alias(['role', \Presentation\Http\Middleware\RoleMiddleware::class]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
