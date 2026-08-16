<?php
// Path: app/Modules/Projects/Budgets/Http/Controllers/BudgetController.php

declare(strict_types=1);

namespace App\Modules\Projects\Budgets\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Budgets\Application\ProjectBudgetService;

class BudgetController extends Controller
{
    protected ProjectBudgetService $budgetService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ProjectBudgetService $budgetService, 
        Gate $gate, 
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->budgetService = $budgetService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function allocate(Request $request, int $projectId): JsonResponse
    {
        $this->gate->authorize('projects', 'budgets', 'update');
        
        $data = $this->inputGuard->getCleanPayload($request);
        $amount = (float) ($data['allocated_amount'] ?? 0);
        $companyId = $this->tenant->requireTenant()->companyId;

        $this->budgetService->allocateBudget($projectId, $amount, $companyId);

        return ApiResponse::success(null, "Budget allocated to project successfully.");
    }
}