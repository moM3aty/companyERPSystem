<?php
// Path: app/Modules/Projects/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Projects\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Projects (PMO) Dashboard
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
        $this->gate->authorize('projects', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $projects = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt, SUM(budget) as total_budget FROM projects WHERE company_id = ? AND status = 'active'", [$companyId]);
        $overdueTasks = $this->db->connection()->selectOne(
            "SELECT COUNT(t.id) as cnt FROM project_tasks t JOIN projects p ON t.project_id = p.id WHERE p.company_id = ? AND t.status != 'done' AND t.due_date < NOW()",
            [$companyId]
        );

        $data = [
            'active_projects_count'  => (int) ($projects['cnt'] ?? 0),
            'total_active_budget'    => (float) ($projects['total_budget'] ?? 0.0),
            'overdue_tasks_count'    => (int) ($overdueTasks['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Projects PMO dashboard metrics retrieved.');
    }
}