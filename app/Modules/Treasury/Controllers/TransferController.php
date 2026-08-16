<?php
// Path: app/Modules/Treasury/Controllers/TransferController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Treasury\Services\TransferService;
use App\Modules\Treasury\Requests\CreateTransferRequest;

/**
 * Enterprise API Controller: Treasury Transfers
 */
class TransferController extends Controller
{
    protected TransferService $transferService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        TransferService $transferService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->transferService = $transferService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, CreateTransferRequest $validator): JsonResponse
    {
        $this->gate->authorize('treasury', 'transfers', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $transfer = $this->transferService->executeTransfer($validatedData, $companyId, $userId);

        return ApiResponse::created($transfer, 'Funds transferred successfully and journal entry posted.');
    }
}