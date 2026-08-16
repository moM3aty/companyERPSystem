<?php
// Path: app/Modules/Payroll/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Payroll\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Payroll Dashboard
 * يعرض إحصائيات آخر مسير رواتب.
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
        $this->gate->authorize('payroll', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        // جلب أحدث مسير رواتب معتمد
        $lastRun = $this->db->connection()->selectOne(
            "SELECT run_period, net_total FROM payroll_runs WHERE company_id = ? AND status IN ('posted', 'approved') ORDER BY run_period DESC LIMIT 1",
            [$companyId]
        );

        $draftRuns = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as cnt FROM payroll_runs WHERE company_id = ? AND status = 'draft'",
            [$companyId]
        );

        $data = [
            'last_processed_period'  => $lastRun['run_period'] ?? 'N/A',
            'last_net_payout'        => (float) ($lastRun['net_total'] ?? 0.0),
            'pending_draft_runs'     => (int) ($draftRuns['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Payroll dashboard metrics retrieved.');
    }
}