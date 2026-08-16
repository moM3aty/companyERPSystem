<?php
// Path: app/Modules/HR/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\HR\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: HR Dashboard
 * يعرض قوة العمل، الإجازات المعلقة، والعقود.
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
        $this->gate->authorize('hr', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $today = date('Y-m-d');

        $employees = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM hr_employees WHERE company_id = ? AND status = 'active'", [$companyId]);
        $pendingLeaves = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM hr_leave_requests WHERE company_id = ? AND status = 'pending'", [$companyId]);
        $absencesToday = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM hr_attendance_records WHERE company_id = ? AND record_date = ? AND status = 'absent'", [$companyId, $today]);

        $data = [
            'active_employees'       => (int) ($employees['cnt'] ?? 0),
            'pending_leave_requests' => (int) ($pendingLeaves['cnt'] ?? 0),
            'today_absences'         => (int) ($absencesToday['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'HR dashboard metrics retrieved.');
    }
}