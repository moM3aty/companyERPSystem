<?php
// Path: app/Modules/CRM/Opportunities/Http/Controllers/OpportunityController.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\CRM\Opportunities\Application\OpportunityService;
use App\Modules\CRM\Opportunities\Http\Requests\StoreOpportunityRequest;

class OpportunityController extends Controller
{
    protected OpportunityService $opportunityService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        OpportunityService $opportunityService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->opportunityService = $opportunityService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreOpportunityRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'opportunities', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $opportunityId = $this->opportunityService->createOpportunity($validatedData, $companyId, $userId);

        return ApiResponse::created(['opportunity_id' => $opportunityId], 'Sales Opportunity added to the pipeline successfully.');
    }
}