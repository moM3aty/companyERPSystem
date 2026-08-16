<?php
// Path: app/Core/Http/Middleware/AuditMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Monitoring\Logger;
use App\Core\Monitoring\LogContext;
use App\Core\Auth\AuthManager;
use App\Core\Tenant\TenantManager;

/**
 * Enterprise Audit Middleware
 * يسجل كل طلب صادر و وارد للنظام لتكوين Audit Trail كامل متوافق مع معايير الأمان المالية (Compliance).
 */
class AuditMiddleware implements MiddlewareInterface
{
    protected Logger $logger;
    protected AuthManager $auth;
    protected TenantManager $tenant;

    public function __construct(Logger $logger, AuthManager $auth, TenantManager $tenant)
    {
        $this->logger = $logger;
        $this->auth = $auth;
        $this->tenant = $tenant;
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // استخراج سياق الطلب
        $userId = $this->auth->user()?->id;
        $companyId = $this->tenant->getCurrentTenant()?->companyId;
        $ip = $request->server('REMOTE_ADDR', 'Unknown');
        $userAgent = $request->server('HTTP_USER_AGENT', 'Unknown');
        
        $context = new LogContext($userId, $companyId, $ip, $userAgent);
        $this->logger->setDefaultContext($context);

        // إخفاء البيانات الحساسة من الـ Log
        $payload = $this->maskSensitiveData($_POST);

        $this->logger->info("Incoming Request: {$request->method()} {$request->uri()}", [
            'payload' => empty($payload) ? null : $payload
        ]);

        /** @var Response $response */
        $response = $next($request);

        $executionTime = round((microtime(true) - $startTime) * 1000, 2); // بالمللي ثانية

        $this->logger->info("Outgoing Response: {$response->getContent() ? 'Has Content' : 'Empty'}", [
            'status' => $response->getStatusCode() ?? 200,
            'execution_time_ms' => $executionTime
        ]);

        return $response;
    }

    /**
     * حجب الحقول الحساسة (مثل كلمات المرور وأرقام البطاقات) قبل كتابتها في السجلات.
     *
     * @param array $data
     * @return array
     */
    protected function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'credit_card', 'cvv', 'secret'];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            } elseif (in_array(strtolower($key), $sensitiveKeys, true)) {
                $data[$key] = '********';
            }
        }

        return $data;
    }
}