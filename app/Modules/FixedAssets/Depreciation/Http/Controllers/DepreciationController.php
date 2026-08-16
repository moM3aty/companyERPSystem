<?php
// Path: app/Modules/FixedAssets/Depreciation/Http/Controllers/DepreciationController.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\ApiError;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\FixedAssets\Depreciation\Application\DepreciationService;

/**
 * Enterprise API Controller: Asset Depreciation
 */
class DepreciationController extends Controller
{
    protected DepreciationService $depreciationService;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(
        DepreciationService $depreciationService,
        Gate $gate,
        TenantContext $tenant
    ) {
        $this->depreciationService = $depreciationService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function runDepreciation(Request $request): JsonResponse
    {
        $this->gate->authorize('fixed_assets', 'depreciation', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $assetId = (int) $request->post('asset_id');
        $year = (int) $request->post('year', date('Y'));
        $month = (int) $request->post('month', date('n'));

        if (!$assetId || $month < 1 || $month > 12) {
            return ApiError::error("Invalid parameters. asset_id, year, and month are required.", 422);
        }

        $this->depreciationService->runMonthlyDepreciation($assetId, $year, $month, $companyId);

        return ApiResponse::success(null, "Asset {$assetId} has been successfully depreciated for {$month}/{$year} and the journal entry was dispatched.");
    }
}