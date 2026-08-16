<?php
// Path: app/Modules/POS/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\POS\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: POS Dashboard
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
        $this->gate->authorize('pos', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $today = date('Y-m-d');

        $activeShifts = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM pos_shifts WHERE company_id = ? AND status = 'open'", [$companyId]);
        $todaySales = $this->db->connection()->selectOne("SELECT SUM(grand_total) as total FROM pos_orders WHERE company_id = ? AND status = 'completed' AND DATE(created_at) = ?", [$companyId, $today]);

        $data = [
            'open_cashier_shifts' => (int) ($activeShifts['cnt'] ?? 0),
            'today_pos_revenue'   => (float) ($todaySales['total'] ?? 0.0),
        ];

        return ApiResponse::success($data, 'POS dashboard metrics retrieved.');
    }
}