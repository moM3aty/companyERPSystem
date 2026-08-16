<?php
// Path: app/Modules/Sales/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Sales\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Sales Dashboard
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
        $this->gate->authorize('sales', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $monthStart = date('Y-m-01');

        $revenueMtd = $this->db->connection()->selectOne("SELECT SUM(grand_total) as total FROM sales_invoices WHERE company_id = ? AND status IN ('posted', 'paid') AND invoice_date >= ?", [$companyId, $monthStart]);
        $pendingOrders = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM sales_orders WHERE company_id = ? AND status IN ('confirmed', 'processing')", [$companyId]);

        $data = [
            'revenue_mtd'    => (float) ($revenueMtd['total'] ?? 0.0),
            'pending_orders' => (int) ($pendingOrders['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Sales Dashboard metrics retrieved.');
    }
}