<?php
// Path: app/Core/Http/Middleware/MiddlewareInterface.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Enterprise Middleware Contract
 * Enforces a strict structure for all incoming request filters (Auth, Tenant, Audit, etc.).
 */
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided closure.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function process(Request $request, Closure $next): Response;
}