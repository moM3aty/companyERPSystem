<?php
// Path: app/Modules/Treasury/Controllers/BankController.php

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
use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;

class BankController extends Controller
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
        $this->gate->authorize('treasury', 'banks', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = ValidatorFactory::makeAndValidate($cleanData, [
            'name' => [new Required()],
            'swift_code' => [new Required()],
        ]);

        $bankId = $this->bankService->createBank($validatedData, $companyId);

        return ApiResponse::created(['bank_id' => $bankId], 'Banking institution added successfully.');
    }
}