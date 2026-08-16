<?php
// Path: app/Modules/Inventory/Warehouses/Http/Controllers/WarehouseController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\Warehouses\Application\WarehouseService;
use App\Modules\Inventory\Warehouses\Domain\WarehouseRepositoryInterface;
use App\Modules\Inventory\Warehouses\Http\Requests\StoreWarehouseRequest;

/**
 * Enterprise API Controller: Warehouses
 */
class WarehouseController extends Controller
{
    protected WarehouseService $service;
    protected WarehouseRepositoryInterface $repository;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        WarehouseService $service,
        WarehouseRepositoryInterface $repository,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(): JsonResponse
    {
        $this->gate->authorize('inventory', 'warehouses', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->repository->setTenantId($companyId);

        $warehouses = $this->repository->all();

        return ApiResponse::success($warehouses);
    }

    public function store(Request $request, StoreWarehouseRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'warehouses', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $warehouse = $this->service->createWarehouse($validatedData, $companyId);

        return ApiResponse::created($warehouse->toArray(), 'Warehouse created successfully.');
    }
}