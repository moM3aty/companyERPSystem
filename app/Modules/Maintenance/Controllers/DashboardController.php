<?php
// Path: app/Modules/Maintenance/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Maintenance (CMMS) Dashboard
 */
class DashboardController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;
    protected DatabaseManager $db;

    public function __construct(Gate $gate, TenantContext $tenant, DatabaseManager $db)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->db = $db;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('maintenance', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $activeWos = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM maintenance_work_orders WHERE company_id = ? AND status IN ('pending', 'in_progress')", [$companyId]);
        $criticalWos = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM maintenance_work_orders WHERE company_id = ? AND status != 'completed' AND priority = 'critical'", [$companyId]);

        $data = [
            'active_work_orders'   => (int) ($activeWos['cnt'] ?? 0),
            'critical_work_orders' => (int) ($criticalWos['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Maintenance dashboard metrics retrieved.');
    }
}