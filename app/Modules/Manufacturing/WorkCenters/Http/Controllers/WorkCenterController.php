<?php
// Path: app/Modules/Manufacturing/WorkCenters/Http/Controllers/WorkCenterController.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\WorkCenters\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Manufacturing\WorkCenters\Application\WorkCenterService;

class WorkCenterController extends Controller
{
    protected WorkCenterService $workCenterService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        WorkCenterService $workCenterService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->workCenterService = $workCenterService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'work_centers', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        // Basic validation assumed handled via standard validation factory logic in real app
        $wcId = $this->workCenterService->createWorkCenter($cleanData, $companyId);

        return ApiResponse::created(['work_center_id' => $wcId], 'Work Center configured successfully.');
    }
}