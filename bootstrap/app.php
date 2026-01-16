<?php

use App\Exceptions\Domain\AnimalAlreadyInTargetEnclosureException;
use App\Exceptions\Domain\EnclosureCapacityExceededException;
use App\Exceptions\Domain\InvalidEnvironmentException;
use App\Logging\DomainLogger;
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

        // Append custom middleware classes here...
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {

            // Domain rule violations (409) Conflicts
            if (
                $e instanceof EnclosureCapacityExceededException ||
                $e instanceof AnimalAlreadyInTargetEnclosureException
            ) {
                DomainLogger::warning('Domain rule violation', [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'payload' => $request->all(),
                ]);

                return ApiResponse::error(
                    $e->getMessage(),
                    Response::HTTP_CONFLICT
                );
            }

            if ($e instanceof InvalidEnvironmentException) {
                DomainLogger::warning('Domain environment violation', [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'payload' => $request->all(),
                ]);
                return ApiResponse::error(
                    $e->getMessage(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Model not found (404)
            if ($e instanceof ModelNotFoundException) {
                DomainLogger::warning('Model not found', [
                    'model' => $e->getModel(),
                    'path' => $request->path(),
                ]);

                return ApiResponse::error(
                    'Resource not found.',
                    Response::HTTP_NOT_FOUND
                );
            }

            // Validation errors (422)
            if ($e instanceof ValidationException) {
                DomainLogger::warning('Validation failed', [
                    'errors' => $e->errors(),
                    'path' => $request->path(),
                ]);

                return ApiResponse::error(
                    collect($e->errors())->flatten()->first() ?? 'Validation failed',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Database errors (500)
            if ($e instanceof QueryException) {
                DomainLogger::error('Database error', [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'path' => $request->path(),
                ]);

                return ApiResponse::error(
                    'Database error occurred.',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            // Fallback: unexpected errors (500)
            DomainLogger::error('Unhandled exception', [
                'exception' => class_basename($e),
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'trace' => app()->environment('production') ? null : $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                'An unexpected error occurred.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    })->create();
