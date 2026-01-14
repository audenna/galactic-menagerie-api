<?php

namespace App\Http\Middleware;

use App\Logging\DomainLogger;
use App\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcceptsJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $accept = $request->headers->get('Accept', '');
        DomainLogger::info("Accept received: $accept");

        // If Accept header is missing or does not include application/json
        if (empty($accept) || !str_contains($accept, 'application/json')) {
            return ApiResponse::error(
                'API requests must include Accept: application/json',
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        return $next($request);
    }
}
