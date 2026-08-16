<?php
// Path: app/Modules/HR/EmployeeSelfService/Http/Controllers/EssExpenseClaimController.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Core\Exceptions\AuthorizationException;
use App\Modules\HR\EmployeeSelfService\Application\ExpenseClaimService;
use App\Modules\HR\EmployeeSelfService\Http\Requests\StoreExpenseClaimRequest;

/**
 * Enterprise API Controller: ESS Expense Claims
 * يتيح للموظف عبر الـ Mobile App أو Employee Portal تقديم مطالبات مالية.
 */
class EssExpenseClaimController extends Controller
{
    protected ExpenseClaimService $claimService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ExpenseClaimService $claimService,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->claimService = $claimService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']); // ESS لا يعتمد على Gate بل على الـ employee_id
    }

    public function store(Request $request, StoreExpenseClaimRequest $validator): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $user = $this->auth->user();

        if (!$user || !$user->employeeId) {
            throw new AuthorizationException("Your user account is not linked to an employee profile.");
        }

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'claim_date'  => $validatedData['claim_date'],
            'currency_id' => $validatedData['currency_id'],
            'purpose'     => $validatedData['purpose'],
        ];

        $claimId = $this->claimService->submitClaim($headerData, $validatedData['items'], $user->employeeId, $companyId);

        return ApiResponse::created(['expense_claim_id' => $claimId], 'Expense claim submitted successfully for managerial approval.');
    }
}