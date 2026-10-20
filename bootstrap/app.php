<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The app is served behind a reverse proxy (Render/Cloudflare). Trust its
        // forwarded headers so $request->ip() and HTTPS detection are correct —
        // required for per-visitor rate limiting.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'section' => \App\Http\Middleware\EnsureSectionAccess::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
