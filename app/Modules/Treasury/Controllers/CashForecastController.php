<?php
// Path: app/Modules/Treasury/Controllers/CashForecastController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Treasury\Services\CashForecastService;

/**
 * Enterprise API Controller: Cash Flow Forecasting
 */
class CashForecastController extends Controller
{
    protected CashForecastService $forecastService;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(CashForecastService $forecastService, Gate $gate, TenantContext $tenant)
    {
        $this->forecastService = $forecastService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function generate(Request $request): JsonResponse
    {
        // يتطلب صلاحية إدارية عليا جداً للاطلاع على السيولة (CFO level)
        $this->gate->authorize('treasury', 'cash_forecast', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $daysAhead = (int) $request->query('days', 30);

        if ($daysAhead > 365) {
            $daysAhead = 365; // حماية ضد استهلاك الذاكرة العالي
        }

        $forecast = $this->forecastService->generateForecast($companyId, $daysAhead);

        return ApiResponse::success($forecast, "Cash flow projected successfully for the next {$daysAhead} days.");
    }
}