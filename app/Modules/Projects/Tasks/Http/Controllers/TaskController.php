<?php
// Path: app/Modules/Projects/Tasks/Http/Controllers/TaskController.php

declare(strict_types=1);

namespace App\Modules\Projects\Tasks\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Tasks\Domain\TaskRepositoryInterface;
use App\Modules\Projects\Tasks\Http\Requests\StoreTaskRequest;

/**
 * Enterprise API Controller: Project Tasks
 */
class TaskController extends Controller
{
    protected TaskRepositoryInterface $taskRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        TaskRepositoryInterface $taskRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->taskRepo = $taskRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreTaskRequest $validator): JsonResponse
    {
        $this->gate->authorize('projects', 'tasks', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $validatedData['status'] = 'todo';
        $validatedData['logged_hours'] = 0.00;
        $validatedData['created_at'] = date('Y-m-d H:i:s');

        $taskId = $this->taskRepo->create($validatedData);

        return ApiResponse::created(['task_id' => $taskId], 'Task successfully assigned to the project.');
    }
}