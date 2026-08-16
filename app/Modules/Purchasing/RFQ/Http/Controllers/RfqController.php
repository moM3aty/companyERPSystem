<?php
// Path: app/Modules/Purchasing/RFQ/Http/Controllers/RfqController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\RFQ\Application\RfqService;
use App\Modules\Purchasing\RFQ\Http\Requests\StoreRfqRequest;

class RfqController extends Controller
{
    protected RfqService $rfqService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        RfqService $rfqService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->rfqService = $rfqService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreRfqRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'rfq', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'title'         => $validatedData['title'],
            'deadline_date' => $validatedData['deadline_date'],
            'delivery_date' => $validatedData['delivery_date'] ?? null,
        ];

        $rfqId = $this->rfqService->createRfq($headerData, $validatedData['items'], $validatedData['supplier_ids'], $userId);

        return ApiResponse::created(['rfq_id' => $rfqId], 'Request For Quotation (RFQ) created and sent to suppliers.');
    }
}