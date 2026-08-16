<?php
// Path: app/Modules/Payroll/Controllers/DeductionController.php

declare(strict_types=1);

namespace App\Modules\Payroll\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;
use App\Core\Security\InputGuard;
use App\Modules\Payroll\Repositories\DeductionRepository;

/**
 * Enterprise API Controller: Payroll Deductions
 */
class DeductionController extends Controller
{
    protected DeductionRepository $repo;
    protected TenantContext $tenant;
    protected Gate $gate;
    protected InputGuard $inputGuard;

    public function __construct(
        DeductionRepository $repo, 
        TenantContext $tenant, 
        Gate $gate,
        InputGuard $inputGuard
    ) {
        $this->repo = $repo;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('payroll', 'deductions', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->repo->setTenantId($companyId);
        
        return ApiResponse::success($this->repo->all());
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('payroll', 'deductions', 'create');
        
        $data = $this->inputGuard->getCleanPayload($request);
        $data['company_id'] = $this->tenant->requireTenant()->companyId;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $id = $this->repo->create($data);
        
        return ApiResponse::created(['deduction_id' => $id], 'Deduction rule configured successfully.');
    }
}