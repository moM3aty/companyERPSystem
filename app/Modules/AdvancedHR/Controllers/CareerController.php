<?php
// Path: app/Modules/AdvancedHR/Controllers/CareerController.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\AdvancedHR\Services\CareerService;
use App\Core\Tenant\TenantContext;
use App\Core\Authorization\Gate;
use App\Core\Security\InputGuard;

class CareerController extends Controller
{
    protected CareerService $careerService;
    protected TenantContext $tenant;
    protected Gate $gate;
    protected InputGuard $inputGuard;

    public function __construct(
        CareerService $careerService, 
        TenantContext $tenant, 
        Gate $gate,
        InputGuard $inputGuard
    ) {
        $this->careerService = $careerService;
        $this->tenant = $tenant;
        $this->gate = $gate;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('advanced_hr', 'career_plans', 'create');
        
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $planId = $this->careerService->createCareerPlan($data, $companyId);

        return ApiResponse::created(['career_plan_id' => $planId], 'Career plan created successfully.');
    }
}