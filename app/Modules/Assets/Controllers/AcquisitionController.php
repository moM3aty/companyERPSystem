<?php
// Path: app/Modules/Assets/Controllers/AcquisitionController.php

declare(strict_types=1);

namespace App\Modules\Assets\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Assets\Services\AcquisitionService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class AcquisitionController extends Controller
{
    protected AcquisitionService $acquisitionService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        AcquisitionService $acquisitionService, 
        TenantContext $tenant, 
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->acquisitionService = $acquisitionService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;

        $acquisitionId = $this->acquisitionService->acquireAsset($data, $companyId, $this->auth->user()->id);

        return ApiResponse::created(['acquisition_id' => $acquisitionId], 'Asset acquired and capitalized successfully.');
    }
}