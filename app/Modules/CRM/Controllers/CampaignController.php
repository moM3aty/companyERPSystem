<?php
// Path: app/Modules/CRM/Controllers/CampaignController.php

declare(strict_types=1);

namespace App\Modules\CRM\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\CRM\Services\CampaignService;
use App\Modules\CRM\Requests\StoreCampaignRequest;

class CampaignController extends Controller
{
    protected CampaignService $campaignService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        CampaignService $campaignService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->campaignService = $campaignService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreCampaignRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'campaigns', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $campaignId = $this->campaignService->createCampaign($validatedData, $companyId, $userId);

        return ApiResponse::created(['campaign_id' => $campaignId], 'Marketing Campaign created successfully.');
    }
}