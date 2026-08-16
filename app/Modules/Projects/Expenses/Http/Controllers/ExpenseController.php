<?php
// Path: app/Modules/Projects/Expenses/Http/Controllers/ExpenseController.php

declare(strict_types=1);

namespace App\Modules\Projects\Expenses\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Expenses\Application\ProjectExpenseService;

/**
 * Enterprise API Controller: Project Expenses
 */
class ExpenseController extends Controller
{
    protected ProjectExpenseService $expenseService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ProjectExpenseService $expenseService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->expenseService = $expenseService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('projects', 'expenses', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $employeeId = $this->auth->user()->employeeId ?? 0;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        // Validation Factory calls assumed here...

        $expenseId = $this->expenseService->logExpense($cleanData, $companyId, $employeeId);

        return ApiResponse::created(['expense_id' => $expenseId], 'Project expense logged successfully.');
    }
}