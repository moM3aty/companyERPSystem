<?php
// Path: app/Modules/APIPlatform/Webhooks/Http/Controllers/WebhookController.php

declare(strict_types=1);

namespace App\Modules\APIPlatform\Webhooks\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\ApiError;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;
use App\Core\Helpers\Str;

/**
 * Enterprise API Controller: Webhook Management
 * واجهة ليقوم العميل من خلال لوحة التحكم بإنشاء Webhooks لربط نظامه بأحداث الـ ERP 
 * (مثلاً: إرسال إشعار لمنصة التجارة الإلكترونية عند تغير رصيد صنف).
 */
class WebhookController extends Controller
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']); // Restricted to Dashboard Admins
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $eventName = $request->post('event_name');
        $targetUrl = $request->post('target_url');

        // Basic Validation
        if (empty($eventName) || !filter_var($targetUrl, FILTER_VALIDATE_URL)) {
            return ApiError::error("Invalid event name or target URL.", 422);
        }

        // توليد سر التوقيع الخاص بهذا الـ Webhook (يُستخدم لعمل HMAC SHA256)
        $secretKey = 'whsec_' . Str::random(32);

        $this->db->connection()->insert(
            "INSERT INTO webhooks (company_id, event_name, target_url, secret_key, is_active, created_at) 
             VALUES (?, ?, ?, ?, 1, ?)",
            [$companyId, $eventName, $targetUrl, $secretKey, date('Y-m-d H:i:s')]
        );

        return ApiResponse::created([
            'event_name' => $eventName,
            'target_url' => $targetUrl,
            'secret_key' => $secretKey // يُعرض مرة واحدة للمطور الخارجي
        ], "Webhook endpoint registered successfully.");
    }
}