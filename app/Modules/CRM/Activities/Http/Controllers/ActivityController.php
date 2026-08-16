<?php
// Path: app/Modules/CRM/Activities/Http/Controllers/ActivityController.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\CRM\Activities\Application\ActivityService;
use App\Modules\CRM\Activities\Http\Requests\StoreActivityRequest;

class ActivityController extends Controller
{
    protected ActivityService $activityService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ActivityService $activityService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->activityService = $activityService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreActivityRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'activities', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $activityId = $this->activityService->logActivity($validatedData, $companyId, $userId);

        return ApiResponse::created(['activity_id' => $activityId], 'CRM Activity logged and scheduled successfully.');
    }
}