<?php
// Path: app/Modules/Fleet/Trips/Http/Controllers/TripController.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Fleet\Application\FleetService;
use App\Modules\Fleet\Trips\Http\Requests\StoreTripRequest;

/**
 * Enterprise API Controller: Fleet Trips
 */
class TripController extends Controller
{
    protected FleetService $fleetService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        FleetService $fleetService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->fleetService = $fleetService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreTripRequest $validator): JsonResponse
    {
        $this->gate->authorize('fleet', 'trips', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $tripId = $this->fleetService->startTrip($validatedData, $companyId, $userId);

        return ApiResponse::created(['trip_id' => $tripId], 'Fleet Trip started successfully.');
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        $this->gate->authorize('fleet', 'trips', 'complete');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);

        // Service will handle extracting distance_covered, trip_cost, fuel_consumed from cleanData
        $this->fleetService->completeTrip($id, $cleanData, $companyId);

        return ApiResponse::success(null, 'Trip completed successfully. Vehicle mileage updated.');
    }
}