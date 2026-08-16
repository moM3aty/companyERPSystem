<?php
// Path: app/Modules/Projects/Milestones/Http/Controllers/MilestoneController.php

declare(strict_types=1);

namespace App\Modules\Projects\Milestones\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Milestones\Application\MilestoneService;

class MilestoneController extends Controller
{
    protected MilestoneService $service;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        MilestoneService $service,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->service = $service;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('projects', 'milestones', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);

        $id = $this->service->createMilestone($cleanData, $companyId, $this->auth->user()->id);

        return ApiResponse::created(['milestone_id' => $id], 'Project Milestone created successfully.');
    }

    public function markAchieved(int $id): JsonResponse
    {
        $this->gate->authorize('projects', 'milestones', 'update');
        $companyId = $this->tenant->requireTenant()->companyId;

        $this->service->achieveMilestone($id, $companyId);

        return ApiResponse::success(null, 'Milestone marked as achieved.');
    }
}