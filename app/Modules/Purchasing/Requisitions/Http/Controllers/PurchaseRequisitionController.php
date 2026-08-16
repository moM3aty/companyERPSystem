<?php
// Path: app/Modules/Purchasing/Requisitions/Http/Controllers/PurchaseRequisitionController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\Requisitions\Application\PurchaseRequisitionService;
use App\Modules\Purchasing\Requisitions\Http\Requests\StorePurchaseRequisitionRequest;

class PurchaseRequisitionController extends Controller
{
    protected PurchaseRequisitionService $prService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PurchaseRequisitionService $prService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->prService = $prService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StorePurchaseRequisitionRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'requisitions', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'department_id' => $validatedData['department_id'],
            'required_date' => $validatedData['required_date'],
            'justification' => $validatedData['justification'],
        ];

        $prId = $this->prService->createRequisition($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['requisition_id' => $prId], 'Purchase Requisition submitted and is pending managerial approval.');
    }
}