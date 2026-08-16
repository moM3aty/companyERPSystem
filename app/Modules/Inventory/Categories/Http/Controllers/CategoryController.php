<?php
// Path: app/Modules/Inventory/Categories/Http/Controllers/CategoryController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\Categories\Application\CategoryService;
use App\Modules\Inventory\Categories\Domain\CategoryRepositoryInterface;
use App\Modules\Inventory\Categories\Http\Requests\StoreCategoryRequest;

/**
 * Enterprise API Controller: Product Categories
 */
class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    protected CategoryRepositoryInterface $categoryRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        CategoryService $categoryService,
        CategoryRepositoryInterface $categoryRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->categoryService = $categoryService;
        $this->categoryRepo = $categoryRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(): JsonResponse
    {
        $this->gate->authorize('inventory', 'categories', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $tree = $this->categoryRepo->getTree($companyId);

        return ApiResponse::success($tree);
    }

    public function store(Request $request, StoreCategoryRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'categories', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $category = $this->categoryService->createCategory($validatedData, $companyId);

        return ApiResponse::created($category->toArray(), 'Product Category created successfully.');
    }
}