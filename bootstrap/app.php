<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            if (request()->is('api/*') || request()->expectsJson()) {
                return match (true) {
                    $e instanceof ValidationException => response()->json([
                        'message' => 'Validation failed.',
                        'errors' => $e->errors(),
                    ], 422),
                    $e instanceof ModelNotFoundException => response()->json([
                        'message' => 'Resource not found.',
                    ], 404),
                    $e instanceof AuthenticationException => response()->json([
                        'message' => 'Unauthenticated.',
                    ], 401),
                    $e instanceof AccessDeniedHttpException => response()->json([
                        'message' => 'Access denied.',
                    ], 403),
                    $e instanceof NotFoundHttpException => response()->json([
                        'message' => 'Endpoint not found.',
                    ], 404),
                    default => response()->json([
                        'message' => 'Internal server error.',
                    ], 500),
                };
            }
        });
    })->create();
