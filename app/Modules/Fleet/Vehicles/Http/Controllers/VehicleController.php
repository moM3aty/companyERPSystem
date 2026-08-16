<?php
// Path: app/Modules/Fleet/Vehicles/Http/Controllers/VehicleController.php

declare(strict_types=1);

namespace App\Modules\Fleet\Vehicles\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Fleet\Application\FleetService;
use App\Modules\Fleet\Vehicles\Http\Requests\StoreVehicleRequest;
use App\Modules\Fleet\Vehicles\Domain\VehicleRepositoryInterface;

/**
 * Enterprise API Controller: Fleet Vehicles
 */
class VehicleController extends Controller
{
    protected FleetService $fleetService;
    protected VehicleRepositoryInterface $vehicleRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        FleetService $fleetService,
        VehicleRepositoryInterface $vehicleRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->fleetService = $fleetService;
        $this->vehicleRepo = $vehicleRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('fleet', 'vehicles', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->vehicleRepo->setTenantId($companyId);

        $vehicles = $this->vehicleRepo->all();

        return ApiResponse::success($vehicles);
    }

    public function store(Request $request, StoreVehicleRequest $validator): JsonResponse
    {
        $this->gate->authorize('fleet', 'vehicles', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $vehicleId = $this->fleetService->createVehicle($validatedData, $companyId);

        return ApiResponse::created(['vehicle_id' => $vehicleId], 'Fleet Vehicle registered successfully.');
    }
}