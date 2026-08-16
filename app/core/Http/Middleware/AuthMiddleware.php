<?php
// Path: app/Core/Http/Middleware/AuthMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\JsonResponse;
use App\Core\Http\RedirectResponse;
use App\Core\Auth\AuthManager;
use App\Core\Auth\TokenManager;
use App\Core\Exceptions\AuthenticationException;

/**
 * Enterprise Authentication Middleware
 * Enforces route protection. Validates active sessions for Web and JWT for API requests.
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected AuthManager $authManager;
    protected TokenManager $tokenManager;

    /**
     * AuthMiddleware constructor.
     *
     * @param AuthManager $authManager
     * @param TokenManager $tokenManager
     */
    public function __construct(AuthManager $authManager, TokenManager $tokenManager)
    {
        $this->authManager = $authManager;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Process an incoming server request to enforce authentication.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function process(Request $request, Closure $next): Response
    {
        $isApiRequest = $request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json');
        
        // 1. Check for API JWT Token
        $authHeader = $request->server('HTTP_AUTHORIZATION', '');
        
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            try {
                // Verify token. If successful, it's valid. (Further integration could bind it to the request context).
                $this->tokenManager->verifyToken($token);
                return $next($request);
            } catch (AuthenticationException $e) {
                return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 401);
            }
        }

        // 2. Check for Web Session Authentication
        if (!$this->authManager->check()) {
            if ($isApiRequest) {
                return new JsonResponse(['status' => 'error', 'message' => 'Unauthenticated access denied.'], 401);
            }
            
            // Redirect web users to the login page
            return new RedirectResponse('/login');
        }

        // 3. Authenticated successfully
        return $next($request);
    }
}