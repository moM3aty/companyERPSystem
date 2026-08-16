<?php
// Path: app/Modules/AdvancedHR/Controllers/CompetencyController.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

class CompetencyController extends Controller
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
        
        $competencies = $this->db->connection()->select(
            "SELECT * FROM advanced_hr_competencies WHERE company_id = ? ORDER BY category, name ASC",
            [$companyId]
        );

        return ApiResponse::success(['competencies' => $competencies]);
    }
}