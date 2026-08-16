<?php
// Path: app/Modules/Manufacturing/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Manufacturing Dashboard
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
        $this->gate->authorize('manufacturing', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $activeBoms = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM manufacturing_boms WHERE company_id = ? AND is_active = 1", [$companyId]);
        $activeOrders = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM manufacturing_production_orders WHERE company_id = ? AND status IN ('planned', 'in_progress')", [$companyId]);

        $data = [
            'active_boms_count'             => (int) ($activeBoms['cnt'] ?? 0),
            'active_production_orders'      => (int) ($activeOrders['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Manufacturing dashboard metrics retrieved.');
    }
}