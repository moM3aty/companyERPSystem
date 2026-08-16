<?php
// Path: app/Modules/Sales/Controllers/CommissionController.php
declare(strict_types=1);

namespace App\Modules\Sales\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;

class CommissionController extends Controller
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;
    protected Gate $gate;

    public function __construct(DatabaseManager $db, TenantContext $tenant, Gate $gate)
    {
        $this->db = $db;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('sales', 'commissions', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $commissions = $this->db->connection()->select(
            "SELECT * FROM sales_commissions WHERE company_id = ? ORDER BY created_at DESC",
            [$companyId]
        );

        return ApiResponse::success(['commissions' => $commissions]);
    }
}