<?php
// Path: app/Modules/Administration/Branches/Http/Controllers/BranchController.php

declare(strict_types=1);

namespace App\Modules\Administration\Branches\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Administration\Branches\Application\BranchService;
use App\Modules\Administration\Branches\Http\Requests\StoreBranchRequest;

class BranchController extends Controller
{
    protected BranchService $branchService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        BranchService $branchService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->branchService = $branchService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreBranchRequest $validator): JsonResponse
    {
        $this->gate->authorize('administration', 'branches', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $branch = $this->branchService->createBranch($validatedData, $companyId);

        return ApiResponse::created($branch->toArray(), 'Company Branch created successfully.');
    }
}