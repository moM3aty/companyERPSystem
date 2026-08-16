<?php
// Path: app/Modules/Fleet/Controllers/DriverController.php

declare(strict_types=1);

namespace App\Modules\Fleet\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Fleet\Services\DriverService;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;
use App\Core\Security\InputGuard;

class DriverController extends Controller
{
    protected DriverService $driverService;
    protected TenantContext $tenant;
    protected Gate $gate;
    protected InputGuard $inputGuard;

    public function __construct(
        DriverService $driverService, 
        TenantContext $tenant, 
        Gate $gate,
        InputGuard $inputGuard
    ) {
        $this->driverService = $driverService;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('fleet', 'drivers', 'create');
        
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;

        $driverId = $this->driverService->registerDriver($data, $companyId);

        return ApiResponse::created(['driver_id' => $driverId], 'Fleet driver registered successfully.');
    }
}