<?php
// Path: app/Modules/HR/Training/Http/Controllers/TrainingController.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\HR\Training\Application\TrainingService;
use App\Modules\HR\Training\Http\Requests\StoreTrainingProgramRequest;

class TrainingController extends Controller
{
    protected TrainingService $trainingService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        TrainingService $trainingService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->trainingService = $trainingService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreTrainingProgramRequest $validator): JsonResponse
    {
        $this->gate->authorize('hr', 'training', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $programId = $this->trainingService->createProgram($validatedData, $companyId, $userId);

        return ApiResponse::created(['training_program_id' => $programId], 'Training program scheduled successfully.');
    }
}