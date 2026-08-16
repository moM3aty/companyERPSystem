<?php
// Path: app/Core/Http/Middleware/RateLimitMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\RateLimiter;
use App\Core\Exceptions\RateLimitException;

/**
 * Enterprise Rate Limit Middleware
 * يقوم بتطبيق قيود عدد الطلبات على الروابط. يمكن تخصيصه لكل رابط (Route).
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    protected RateLimiter $limiter;
    
    /**
     * الحد الأقصى الافتراضي.
     */
    protected int $maxAttempts = 60;
    
    /**
     * مدة الحظر الافتراضية بالثواني.
     */
    protected int $decaySeconds = 60;

    /**
     * RateLimitMiddleware constructor.
     *
     * @param RateLimiter $limiter
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * السماح بتمرير إعدادات مخصصة عبر البارامترات.
     * (مثال لاستدعائه: rate_limit:10,60)
     */
    public function setParameters(int $maxAttempts, int $decaySeconds): self
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        return $this;
    }

    /**
     * Process an incoming server request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function process(Request $request, Closure $next): Response
    {
        // بناء مفتاح فريد بناءً على الـ IP والـ URI لمنع تداخل الحظر
        // في حال وجود مستخدم مسجل دخول، من الأفضل الاعتماد على الـ User ID
        $identifier = $request->server('REMOTE_ADDR', '127.0.0.1');
        
        // محاولة جلب Authorization Header لتمييز الـ API Clients في حال كان نفس الـ IP (خلف Proxy)
        $auth = $request->server('HTTP_AUTHORIZATION');
        if ($auth) {
            $identifier = sha1($auth);
        }

        $key = 'rate_limit:' . $identifier . ':' . $request->uri();

        try {
            $this->limiter->attempt($key, $this->maxAttempts, $this->decaySeconds);
            
            /** @var Response $response */
            $response = $next($request);
            
            // إضافة Headers مفيدة للـ API Clients توضح الحدود
            $remaining = $this->limiter->remaining($key, $this->maxAttempts);
            $response->setHeader('X-RateLimit-Limit', (string) $this->maxAttempts);
            $response->setHeader('X-RateLimit-Remaining', (string) $remaining);
            
            return $response;

        } catch (RateLimitException $e) {
            $response = new Response(json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'retry_after' => $e->retryAfter
            ]), 429);
            
            $response->setHeader('Content-Type', 'application/json');
            $response->setHeader('Retry-After', (string) $e->retryAfter);
            $response->setHeader('X-RateLimit-Limit', (string) $this->maxAttempts);
            $response->setHeader('X-RateLimit-Remaining', '0');
            
            return $response;
        }
    }
}