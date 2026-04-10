<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $throwable, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            if ($throwable instanceof \App\Exceptions\DomainException) {
                return response()->json([
                    'message' => $throwable->getMessage(),
                    'errors' => $throwable->errors(),
                ], $throwable->status());
            }

            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        });
    })->create();
