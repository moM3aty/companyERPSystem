<?php
// Path: app/Modules/Projects/Projects/Http/Controllers/ProjectController.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Projects\Application\ProjectService;
use App\Modules\Projects\Projects\Domain\ProjectRepositoryInterface;
use App\Modules\Projects\Projects\Http\Requests\StoreProjectRequest;

/**
 * Enterprise API Controller: Projects
 */
class ProjectController extends Controller
{
    protected ProjectService $projectService;
    protected ProjectRepositoryInterface $projectRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ProjectService $projectService,
        ProjectRepositoryInterface $projectRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->projectService = $projectService;
        $this->projectRepo = $projectRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('projects', 'projects', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->projectRepo->setTenantId($companyId);

        $projects = $this->projectRepo->all();

        return ApiResponse::success($projects);
    }

    public function store(Request $request, StoreProjectRequest $validator): JsonResponse
    {
        $this->gate->authorize('projects', 'projects', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $project = $this->projectService->createProject($validatedData, $companyId);

        return ApiResponse::created($project->toArray(), 'Project created successfully.');
    }
}