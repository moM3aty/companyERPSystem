<?php
// Path: app/Modules/FixedAssets/Assets/Http/Controllers/AssetController.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\FixedAssets\Assets\Application\AssetService;
use App\Modules\FixedAssets\Assets\Http\Requests\StoreAssetRequest;

/**
 * Enterprise API Controller: Fixed Assets
 */
class AssetController extends Controller
{
    protected AssetService $assetService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        AssetService $assetService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->assetService = $assetService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreAssetRequest $validator): JsonResponse
    {
        $this->gate->authorize('fixed_assets', 'assets', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $asset = $this->assetService->createAsset($validatedData, $companyId);

        return ApiResponse::created($asset->toArray(), 'Fixed Asset registered successfully.');
    }
}