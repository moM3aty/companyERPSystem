<?php
// Path: app/Modules/CRM/Leads/Http/Controllers/LeadController.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\CRM\Leads\Application\LeadService;
use App\Modules\CRM\Leads\Http\Requests\StoreLeadRequest;

class LeadController extends Controller
{
    protected LeadService $leadService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        LeadService $leadService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->leadService = $leadService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreLeadRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'leads', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $leadId = $this->leadService->createLead($validatedData, $companyId);

        return ApiResponse::created(['lead_id' => $leadId], 'CRM Lead captured successfully.');
    }
}