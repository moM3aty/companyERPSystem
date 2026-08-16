<?php
// File 5: app/Modules/SupplyChain/Controllers/LandedCostController.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\SupplyChain\Services\LandedCostService;
use App\Core\Tenant\TenantContext;

class LandedCostController extends Controller
{
    protected LandedCostService $landedCostService;
    protected TenantContext $tenant;

    public function __construct(LandedCostService $landedCostService, TenantContext $tenant)
    {
        $this->landedCostService = $landedCostService;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function allocate(Request $request, int $id): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->landedCostService->allocateCosts($id, $companyId);

        return ApiResponse::success(null, 'Landed costs allocated successfully to the receipt items.');
    }
}