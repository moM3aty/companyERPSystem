<?php
// Path: app/Modules/Purchasing/Suppliers/Http/Controllers/SupplierController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\Suppliers\Application\SupplierService;
use App\Modules\Purchasing\Suppliers\Domain\SupplierRepositoryInterface;
use App\Modules\Purchasing\Suppliers\Http\Requests\StoreSupplierRequest;

/**
 * Enterprise API Controller: Suppliers
 */
class SupplierController extends Controller
{
    protected SupplierService $supplierService;
    protected SupplierRepositoryInterface $supplierRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        SupplierService $supplierService,
        SupplierRepositoryInterface $supplierRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->supplierService = $supplierService;
        $this->supplierRepo = $supplierRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('purchasing', 'suppliers', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->supplierRepo->setTenantId($companyId);

        $suppliers = $this->supplierRepo->all();

        return ApiResponse::success($suppliers);
    }

    public function store(Request $request, StoreSupplierRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'suppliers', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $supplier = $this->supplierService->createSupplier($validatedData, $companyId);

        return ApiResponse::created($supplier->toArray(), 'Supplier created successfully.');
    }
}