<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ErrorResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Exceptions\AuthenticationException as ExceptionsAuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('ensure-admin-role', [
            App\Http\Middleware\EnsureAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException| ExceptionsAuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                $error = new \Exception($exception->getMessage(), Response::HTTP_UNAUTHORIZED);

                return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
            }
        });
    })->create();
