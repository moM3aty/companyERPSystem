<?php
// Path: app/Modules/AdvancedPricing/Controllers/DiscountController.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

class DiscountController extends Controller
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
        
        $rules = $this->db->connection()->select(
            "SELECT * FROM advanced_pricing_discount_rules WHERE company_id = ? ORDER BY discount_percentage DESC",
            [$companyId]
        );

        return ApiResponse::success(['discount_rules' => $rules]);
    }
}