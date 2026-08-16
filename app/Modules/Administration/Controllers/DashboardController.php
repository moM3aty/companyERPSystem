<?php
// Path: app/Modules/Administration/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Administration\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Controller: Administration Dashboard
 * يجمع البيانات التحليلية الرئيسية الخاصة بموديول الإدارة.
 */
class DashboardController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(Gate $gate, TenantContext $tenant)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('administration', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        // في النظام الفعلي سيتم سحب البيانات من الـ Metrics أو الـ Dashboard Service
        $data = [
            'active_users'     => 145,
            'pending_invites'  => 12,
            'active_branches'  => 4,
            'security_alerts'  => 2,
        ];

        return ApiResponse::success($data, 'Administration dashboard metrics retrieved.');
    }
}