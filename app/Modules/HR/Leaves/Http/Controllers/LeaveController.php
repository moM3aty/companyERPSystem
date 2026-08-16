<?php
// Path: app/Modules/HR/Leaves/Http/Controllers/LeaveController.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\HR\Leaves\Application\LeaveService;
use App\Modules\HR\Leaves\Http\Requests\StoreLeaveRequestRequest;

class LeaveController extends Controller
{
    protected LeaveService $leaveService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        LeaveService $leaveService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->leaveService = $leaveService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreLeaveRequestRequest $validator): JsonResponse
    {
        $this->gate->authorize('hr', 'leaves', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $leaveId = $this->leaveService->submitLeaveRequest($validatedData, $companyId);

        return ApiResponse::created(['leave_request_id' => $leaveId], 'Leave request submitted successfully.');
    }

    public function approve(int $id): JsonResponse
    {
        $this->gate->authorize('hr', 'leaves', 'approve');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $approverId = $this->auth->user()->id;

        $this->leaveService->approveLeave($id, $approverId, $companyId);

        return ApiResponse::success(null, 'Leave request approved successfully.');
    }
}