<?php
// Path: app/Modules/HR/Positions/Http/Controllers/PositionController.php

declare(strict_types=1);

namespace App\Modules\HR\Positions\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\HR\Positions\Application\PositionService;

/**
 * Enterprise API Controller: HR Positions
 */
class PositionController extends Controller
{
    protected PositionService $positionService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        PositionService $positionService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->positionService = $positionService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('hr', 'positions', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);

        $positionId = $this->positionService->createPosition($cleanData, $companyId);

        return ApiResponse::created(['position_id' => $positionId], 'Job position defined successfully.');
    }
}