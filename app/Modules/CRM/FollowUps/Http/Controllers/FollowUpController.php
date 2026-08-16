<?php
// Path: app/Modules/CRM/FollowUps/Http/Controllers/FollowUpController.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\CRM\FollowUps\Application\FollowUpService;
use App\Modules\CRM\FollowUps\Http\Requests\StoreFollowUpRequest;

/**
 * Enterprise API Controller: CRM Follow-Ups
 */
class FollowUpController extends Controller
{
    protected FollowUpService $followUpService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        FollowUpService $followUpService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->followUpService = $followUpService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreFollowUpRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'follow_ups', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $id = $this->followUpService->scheduleFollowUp($validatedData, $companyId, $userId);

        return ApiResponse::created(['follow_up_id' => $id], 'Follow-up reminder scheduled successfully.');
    }

    public function complete(int $id): JsonResponse
    {
        $this->gate->authorize('crm', 'follow_ups', 'complete');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $this->followUpService->completeFollowUp($id, $companyId, $userId);

        return ApiResponse::success(null, 'Follow-up marked as completed.');
    }
}