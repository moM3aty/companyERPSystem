<?php
// Path: app/Compliance/DataRetention/Http/Controllers/DataRetentionController.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Compliance\DataRetention\Domain\RetentionPolicyRepositoryInterface;
use App\Compliance\DataRetention\Http\Requests\StoreRetentionPolicyRequest;

/**
 * Enterprise API Controller: Data Retention Policies (Compliance)
 */
class DataRetentionController extends Controller
{
    protected RetentionPolicyRepositoryInterface $policyRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        RetentionPolicyRepositoryInterface $policyRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->policyRepo = $policyRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreRetentionPolicyRequest $validator): JsonResponse
    {
        $this->gate->authorize('compliance', 'retention_policies', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $validatedData['company_id'] = $companyId;
        $validatedData['is_active'] = $validatedData['is_active'] ?? 1;

        $policyId = $this->policyRepo->create($validatedData);

        return ApiResponse::created(['policy_id' => $policyId], 'Data retention policy configured successfully.');
    }
}