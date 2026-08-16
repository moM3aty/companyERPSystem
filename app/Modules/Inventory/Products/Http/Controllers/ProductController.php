<?php
// Path: app/Modules/Inventory/Products/Http/Controllers/ProductController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\Products\Application\ProductService;
use App\Modules\Inventory\Products\Domain\ProductRepositoryInterface;
use App\Modules\Inventory\Products\Http\Requests\StoreProductRequest;

/**
 * Enterprise API Controller: Products
 * نقطة الدخول لإدارة الأصناف والمخزون.
 */
class ProductController extends Controller
{
    protected ProductService $service;
    protected ProductRepositoryInterface $repository;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ProductService $service,
        ProductRepositoryInterface $repository,
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

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('inventory', 'products', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->repository->setTenantId($companyId);

        // لاحقاً يتم استبدالها بـ pagination & search 
        $products = $this->repository->all();

        return ApiResponse::success($products);
    }

    public function store(Request $request, StoreProductRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'products', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $product = $this->service->createProduct($validatedData, $companyId);

        return ApiResponse::created($product->toArray(), 'Product created successfully.');
    }
}