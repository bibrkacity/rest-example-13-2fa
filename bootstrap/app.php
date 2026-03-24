<?php

use App\Exceptions\ApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, Illuminate\Http\Request|Illuminate\Support\Facades\Request|Request $request) {

            if ($request->is('api/*')) {
                if ($e instanceof ApiException) {
                    return $e->render();
                } else {
                    return (new ApiException(message: $e->getMessage()))->render();
                }

            } else {
                return ''; // Stub for the future
            }

        });

    })->create();
