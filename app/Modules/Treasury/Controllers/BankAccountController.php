<?php
// Path: app/Modules/Treasury/Controllers/BankAccountController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Treasury\Services\BankService;

class BankAccountController extends Controller
{
    protected BankService $bankService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(BankService $bankService, Gate $gate, TenantContext $tenant, InputGuard $inputGuard)
    {
        $this->bankService = $bankService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('treasury', 'bank_accounts', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        // Assuming validation is handled via standard logic
        
        $accountId = $this->bankService->createBankAccount($cleanData, $companyId);

        return ApiResponse::created(['account_id' => $accountId], 'Bank Account linked to General Ledger successfully.');
    }
}