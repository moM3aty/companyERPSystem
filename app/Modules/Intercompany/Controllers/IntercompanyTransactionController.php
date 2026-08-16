<?php
// Path: app/Modules/Intercompany/Controllers/IntercompanyTransactionController.php
declare(strict_types=1);

namespace App\Modules\Intercompany\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

class IntercompanyTransactionController extends Controller
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $transactions = $this->db->connection()->select(
            "SELECT * FROM intercompany_transactions WHERE from_company_id = ? OR to_company_id = ? ORDER BY created_at DESC",
            [$companyId, $companyId]
        );

        return ApiResponse::success(['transactions' => $transactions]);
    }
}