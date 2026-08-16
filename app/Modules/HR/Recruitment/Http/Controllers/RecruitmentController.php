<?php
// Path: app/Modules/HR/Recruitment/Http/Controllers/RecruitmentController.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\HR\Recruitment\Application\RecruitmentService;
use App\Modules\HR\Recruitment\Http\Requests\StoreJobOpeningRequest;
use App\Modules\HR\Recruitment\Http\Requests\StoreApplicantRequest;

class RecruitmentController extends Controller
{
    protected RecruitmentService $recruitmentService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        RecruitmentService $recruitmentService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->recruitmentService = $recruitmentService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
    }

    /**
     * نشر وظيفة شاغرة جديدة (للـ HR).
     */
    public function storeJobOpening(Request $request, StoreJobOpeningRequest $validator): JsonResponse
    {
        // Require Authentication for internal HR actions
        if (!$this->auth->check()) {
            return \App\Core\Api\ApiError::unauthorized();
        }

        $this->gate->authorize('hr', 'recruitment', 'create_job');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $jobId = $this->recruitmentService->createJobOpening($validatedData, $companyId, $userId);

        return ApiResponse::created(['job_opening_id' => $jobId], 'Job opening published successfully.');
    }

    /**
     * استقبال طلب توظيف من متقدم خارجي.
     * لا يمر عبر הـ Gate لأنه Public Endpoint (متاح للجميع للتقديم عبر الموقع).
     */
    public function apply(Request $request, StoreApplicantRequest $validator): JsonResponse
    {
        // Note: Company ID should ideally be resolved via header/domain in Public APIs
        $companyId = $this->tenant->requireTenant()->companyId; 

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $applicantId = $this->recruitmentService->applyForJob($validatedData, $companyId);

        return ApiResponse::created(['applicant_id' => $applicantId], 'Your application has been submitted successfully.');
    }
}