<?php
// Path: app/Core/Http/Middleware/ApiKeyMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\JsonResponse;
use App\Modules\APIPlatform\Keys\Application\ApiKeyManager;
use App\Core\Tenant\TenantContext;
use App\Core\Tenant\Tenant;

/**
 * Enterprise API Key Middleware
 * يحمي الروابط المخصصة للـ Public API/M2M Integration.
 * يعترض الطلب، يحلل الـ X-API-Key، ويقوم بتفعيل الـ Tenant المناسب تلقائياً.
 */
class ApiKeyMiddleware implements MiddlewareInterface
{
    protected ApiKeyManager $keyManager;
    protected TenantContext $tenantContext;

    public function __construct(ApiKeyManager $keyManager, TenantContext $tenantContext)
    {
        $this->keyManager = $keyManager;
        $this->tenantContext = $tenantContext;
    }

    public function process(Request $request, Closure $next): Response
    {
        $providedKey = $request->server('HTTP_X_API_KEY');

        if (empty($providedKey)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Missing X-API-Key header.'], 401);
        }

        try {
            $apiKey = $this->keyManager->authenticate($providedKey);

            // التحقق من الـ IP إذا كانت هناك قيود
            $allowedIps = $apiKey->getAttribute('allowed_ips');
            if (is_array($allowedIps) && !empty($allowedIps)) {
                $clientIp = $request->server('REMOTE_ADDR');
                if (!in_array($clientIp, $allowedIps, true)) {
                    return new JsonResponse(['status' => 'error', 'message' => 'IP address not whitelisted for this API Key.'], 403);
                }
            }

            // إجبار النظام على الدخول في سياق شركة هذا المفتاح (Tenant Context injection)
            $this->tenantContext->setTenant(new Tenant((int) $apiKey->company_id));

            // يمكن تخزين الـ Scopes في الـ Request لاستخدامها لاحقاً في الكنترولرز

        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}