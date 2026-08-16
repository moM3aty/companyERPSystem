<?php
// Path: app/Modules/HR/Performance/Http/Controllers/PerformanceController.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\HR\Performance\Application\PerformanceService;
use App\Modules\HR\Performance\Http\Requests\StoreAppraisalRequest;

/**
 * Enterprise API Controller: Performance Appraisals
 */
class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PerformanceService $performanceService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->performanceService = $performanceService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreAppraisalRequest $validator): JsonResponse
    {
        $this->gate->authorize('hr', 'performance', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $evaluatorId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $appraisalId = $this->performanceService->submitAppraisal($validatedData, $companyId, $evaluatorId);

        return ApiResponse::created(['appraisal_id' => $appraisalId], 'Employee performance appraisal submitted successfully.');
    }
}