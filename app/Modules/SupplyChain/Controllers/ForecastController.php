<?php
// Path: app/Modules/SupplyChain/Controllers/ForecastController.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\SupplyChain\Services\ForecastService;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;

class ForecastController extends Controller
{
    protected ForecastService $forecastService;
    protected TenantContext $tenant;
    protected Gate $gate;

    public function __construct(ForecastService $forecastService, TenantContext $tenant, Gate $gate)
    {
        $this->forecastService = $forecastService;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function runForecast(Request $request): JsonResponse
    {
        $this->gate->authorize('supply_chain', 'forecasts', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $monthsLookback = (int)($request->query('lookback_months') ?? 3);

        $count = $this->forecastService->generateForecast($companyId, $monthsLookback);

        return ApiResponse::created(['forecasts_generated' => $count], "Demand forecasts generated successfully for the next month.");
    }
}