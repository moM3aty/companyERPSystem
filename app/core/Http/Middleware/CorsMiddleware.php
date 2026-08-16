<?php
// Path: app/Core/Http/Middleware/CorsMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Enterprise CORS Middleware
 * Handles Cross-Origin Resource Sharing headers safely for external API integrations.
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request to add CORS headers.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function process(Request $request, Closure $next): Response
    {
        // 1. Intercept Pre-flight OPTIONS requests
        if ($request->method() === 'OPTIONS') {
            // Return empty response with 204 No Content for pre-flight
            $response = new Response('', 204);
        } else {
            // Process the actual request through the pipeline
            /** @var Response $response */
            $response = $next($request);
        }

        // 2. Attach the required CORS headers
        // In a strict production environment, '*' should be replaced by a configured list of allowed domains
        $response->setHeader('Access-Control-Allow-Origin', '*');
        
        // Define allowed HTTP methods
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        
        // Define allowed headers, including custom Enterprise headers (e.g., X-Company-Id for Tenants)
        $response->setHeader(
            'Access-Control-Allow-Headers', 
            'Content-Type, Authorization, X-Requested-With, X-Company-Id, X-Branch-Id, Accept'
        );
        
        // Allow credentials (cookies/authorization headers) if strictly needed
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        // Cache pre-flight response for 1 hour to reduce server load
        $response->setHeader('Access-Control-Max-Age', '3600');

        return $response;
    }
}