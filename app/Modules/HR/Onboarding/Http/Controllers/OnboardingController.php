<?php
// Path: app/Modules/HR/Onboarding/Http/Controllers/OnboardingController.php

declare(strict_types=1);

namespace App\Modules\HR\Onboarding\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Modules\HR\Onboarding\Application\OnboardingService;
use App\Modules\HR\Onboarding\Domain\OnboardingTaskRepositoryInterface;

class OnboardingController extends Controller
{
    protected OnboardingService $service;
    protected OnboardingTaskRepositoryInterface $taskRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(
        OnboardingService $service,
        OnboardingTaskRepositoryInterface $taskRepo,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth
    ) {
        $this->service = $service;
        $this->taskRepo = $taskRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function completeTask(int $id): JsonResponse
    {
        $this->gate->authorize('hr', 'onboarding', 'update');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $this->service->completeTask($id, $userId, $companyId);

        return ApiResponse::success(null, 'Onboarding task marked as completed.');
    }
}