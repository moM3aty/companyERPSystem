<?php
// Path: app/Modules/CRM/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\CRM\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: CRM Dashboard
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
        $this->gate->authorize('crm', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $monthStart = date('Y-m-01');

        $newLeads = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM crm_leads WHERE company_id = ? AND created_at >= ?", [$companyId, $monthStart]);
        $openOpps = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM crm_opportunities WHERE company_id = ? AND stage NOT IN ('closed_won', 'closed_lost')", [$companyId]);
        $pipelineValue = $this->db->connection()->selectOne("SELECT SUM(expected_revenue) as total FROM crm_opportunities WHERE company_id = ? AND stage NOT IN ('closed_won', 'closed_lost')", [$companyId]);

        $data = [
            'new_leads_mtd'      => (int) ($newLeads['cnt'] ?? 0),
            'open_opportunities' => (int) ($openOpps['cnt'] ?? 0),
            'pipeline_value'     => (float) ($pipelineValue['total'] ?? 0.0),
        ];

        return ApiResponse::success($data, 'CRM Dashboard metrics retrieved.');
    }
}