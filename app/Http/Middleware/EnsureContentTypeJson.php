<?php

namespace App\Http\Middleware;

use App\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContentTypeJson
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate for methods that typically have a body
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $contentType = $request->headers->get('Content-Type', '');

            if (empty($contentType) || ! str_contains($contentType, 'application/json')) {
                return ApiResponse::error(
                    'Content-Type must be application/json',
                    Response::HTTP_UNSUPPORTED_MEDIA_TYPE
                );
            }
        }

        return $next($request);
    }
}
