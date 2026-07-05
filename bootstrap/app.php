<?php

use App\Exceptions\ApiException;
use App\Exceptions\ValidationException as ApiValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Validation\ValidationException;

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

        $exceptions->render(function (Throwable $e, Request|FacadesRequest $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ApiException) { /** We throw this exception when we want to return a custom error */
                    return $e->render();
                } elseif ($e instanceof ValidationException) { /** FormRequest throws this exception */
                    return new ApiValidationException(
                        message: $e->getMessage(),
                    )->render();
                } else {  /** Unexpected exception */
                    return new ApiException(
                        message: $e->getMessage(),
                        args: ['class' => get_class($e), 'file' => $e->getFile(), 'line' => $e->getLine()],
                    )->render();
                }
            }

            return $e->getMessage();
        });

    })->create();
