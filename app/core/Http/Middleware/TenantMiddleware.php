<?php
// Path: app/Core/Http/Middleware/TenantMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\JsonResponse;
use App\Core\Tenant\TenantManager;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Tenant Middleware
 * Ensures that the request operates within a specific Company/Branch scope.
 * Critical for Multi-Tenant Data Isolation.
 */
class TenantMiddleware implements MiddlewareInterface
{
    protected TenantManager $tenantManager;

    /**
     * TenantMiddleware constructor.
     *
     * @param TenantManager $tenantManager
     */
    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Process an incoming server request to enforce Tenant scoping.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function process(Request $request, Closure $next): Response
    {
        // 1. Attempt to initialize the tenant from Headers or Session
        $isResolved = $this->tenantManager->initialize($request);

        // 2. Block access if no valid tenant context is found
        if (!$isResolved) {
            $isApiRequest = $request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json');
            $message = 'Tenant Context is missing. A valid Company ID must be provided to access this resource.';

            if ($isApiRequest) {
                return new JsonResponse(['status' => 'error', 'message' => $message], 403);
            }

            // For web interfaces, we might render an error view or redirect to a company-selection page
            return new Response($this->buildErrorHtml($message), 403);
        }

        // 3. Context resolved successfully, proceed to the next middleware or controller
        return $next($request);
    }

    /**
     * Build a simple HTML error page for missing tenants on Web Requests.
     *
     * @param string $message
     * @return string
     */
    protected function buildErrorHtml(string $message): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Missing Company Context</title>
            <style>
                body { font-family: system-ui, sans-serif; background: #f9fafb; color: #111827; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); max-width: 500px; text-align: center; border-top: 4px solid #ef4444; }
                h1 { margin-top: 0; font-size: 1.5rem; }
                p { color: #4b5563; line-height: 1.5; }
                a { display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Access Denied (403)</h1>
                <p>{$message}</p>
                <a href="/">Return to Dashboard</a>
            </div>
        </body>
        </html>
        HTML;
    }
}