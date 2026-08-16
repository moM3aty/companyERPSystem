<?php
// Path: app/Modules/AdvancedHR/Controllers/PerformanceController.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\AdvancedHR\Services\PerformanceService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PerformanceService $performanceService, 
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->performanceService = $performanceService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;
        
        // Ensure reviewer is the authenticated user
        $data['reviewer_id'] = $this->auth->user()->id;

        $reviewId = $this->performanceService->submitReview($data, $companyId);

        return ApiResponse::created(['review_id' => $reviewId], 'Performance review submitted successfully.');
    }
}