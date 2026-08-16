<?php
// Path: app/Modules/Administration/Controllers/SystemHealthController.php

declare(strict_types=1);

namespace App\Modules\Administration\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Monitoring\HealthCheck;

/**
 * Enterprise Controller: System Health
 * نقطة فحص (Ping/Probe) لفرق الـ DevOps لمراقبة حالة السيرفرات وقواعد البيانات.
 */
class SystemHealthController extends Controller
{
    protected Gate $gate;
    protected HealthCheck $healthCheck;

    public function __construct(Gate $gate, HealthCheck $healthCheck)
    {
        $this->gate = $gate;
        $this->healthCheck = $healthCheck;
    }

    public function check(Request $request): JsonResponse
    {
        // حماية الـ Endpoint: مسموح للـ Super Admins فقط
        $this->gate->authorize('administration', 'system_health', 'view');
        
        $status = $this->healthCheck->run();

        $httpCode = $status['status'] === 'healthy' ? 200 : 503;

        return ApiResponse::success($status, 'System health report generated.', $httpCode);
    }
}