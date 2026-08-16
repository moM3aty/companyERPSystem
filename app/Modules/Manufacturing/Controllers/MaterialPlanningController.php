<?php
// Path: app/Modules/Manufacturing/Controllers/MaterialPlanningController.php
declare(strict_types=1);

namespace App\Modules\Manufacturing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Manufacturing\Services\MaterialPlanningService;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;

class MaterialPlanningController extends Controller
{
    protected MaterialPlanningService $mrpService;
    protected TenantContext $tenant;
    protected Gate $gate;

    public function __construct(MaterialPlanningService $mrpService, TenantContext $tenant, Gate $gate)
    {
        $this->mrpService = $mrpService;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function calculateRequirements(Request $request, int $productionOrderId): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'mrp', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $shortages = $this->mrpService->runMRPForOrder($productionOrderId, $companyId);

        return ApiResponse::success(['shortages' => $shortages], 'MRP calculation completed for the production order.');
    }
}