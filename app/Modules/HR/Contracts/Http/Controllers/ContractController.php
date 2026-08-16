<?php
// Path: app/Modules/HR/Contracts/Http/Controllers/ContractController.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\HR\Contracts\Application\ContractService;
use App\Modules\HR\Contracts\Http\Requests\StoreContractRequest;

/**
 * Enterprise API Controller: Employee Contracts
 */
class ContractController extends Controller
{
    protected ContractService $contractService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ContractService $contractService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->contractService = $contractService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreContractRequest $validator): JsonResponse
    {
        // يتطلب صلاحية إنشاء العقود (عادة لمدير الموارد البشرية)
        $this->gate->authorize('hr', 'contracts', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $contract = $this->contractService->issueContract($validatedData, $companyId);

        return ApiResponse::created($contract->toArray(), 'Employee Contract issued successfully.');
    }
}