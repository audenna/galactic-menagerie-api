<?php

use App\Exceptions\Domain\AnimalAlreadyInTargetEnclosureException;
use App\Exceptions\Domain\EnclosureCapacityExceededException;
use App\Exceptions\Domain\InvalidEnvironmentException;
use App\Http\Middleware\EnsureAcceptsJson;
use App\Http\Middleware\EnsureContentTypeJson;
use App\Responses\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Middleware\ValidatePostSize;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // The Laravel Default middlewares
        $middleware->use([
            InvokeDeferredCallbacks::class,
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        // Append custom middleware classes here
        $middleware->append(EnsureAcceptsJson::class);
        $middleware->append(EnsureContentTypeJson::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            return match (true) {
                $e instanceof EnclosureCapacityExceededException, $e instanceof AnimalAlreadyInTargetEnclosureException =>
                ApiResponse::error($e->getMessage(), Response::HTTP_CONFLICT),

                $e instanceof InvalidEnvironmentException =>
                ApiResponse::error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY),

                $e instanceof ModelNotFoundException =>
                ApiResponse::error('Resource not found.', Response::HTTP_NOT_FOUND),

                // Handle default validation error and return only the first validation error
                $e instanceof ValidationException =>
                ApiResponse::error(
                    collect($e->errors())->flatten()->first() ?? 'Validation failed',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                ),

                $e instanceof QueryException =>
                ApiResponse::error('Database error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR),

                default => ApiResponse::error(
                    'An unexpected error occurred: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ),
            };
        });
    })->create();
