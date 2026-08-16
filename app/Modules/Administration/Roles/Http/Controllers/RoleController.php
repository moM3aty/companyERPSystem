<?php
// Path: app/Modules/Administration/Roles/Http/Controllers/RoleController.php

declare(strict_types=1);

namespace App\Modules\Administration\Roles\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Administration\Roles\Application\RoleService;
use App\Modules\Administration\Roles\Domain\RoleRepositoryInterface;
use App\Modules\Administration\Roles\Http\Requests\StoreRoleRequest;

/**
 * Enterprise API Controller: Role Management
 */
class RoleController extends Controller
{
    protected RoleService $roleService;
    protected RoleRepositoryInterface $roleRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        RoleService $roleService,
        RoleRepositoryInterface $roleRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->roleService = $roleService;
        $this->roleRepo = $roleRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(): JsonResponse
    {
        $this->gate->authorize('administration', 'roles', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->roleRepo->setTenantId($companyId);

        $roles = $this->roleRepo->all();

        return ApiResponse::success($roles);
    }

    public function store(Request $request, StoreRoleRequest $validator): JsonResponse
    {
        $this->gate->authorize('administration', 'roles', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        // التحقق المتقدم باستخدام كلاس الـ Request
        $validatedData = $validator->validate($cleanData);

        $role = $this->roleService->createRoleWithPermissions(
            $validatedData['name'],
            $validatedData['description'] ?? '',
            $validatedData['permission_ids'] ?? [],
            $companyId
        );

        return ApiResponse::created($role, 'Role created successfully.');
    }
}