<?php
// Path: app/Modules/HR/Attendance/Http/Controllers/AttendanceController.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\HR\Attendance\Application\AttendanceService;
use App\Modules\HR\Attendance\Http\Requests\CheckInRequest;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        AttendanceService $attendanceService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->attendanceService = $attendanceService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * تسجيل البصمة للموظف. (يدعم الحضور والانصراف معاً).
     */
    public function punch(Request $request, CheckInRequest $validator): JsonResponse
    {
        $this->gate->authorize('hr', 'attendance', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $result = $this->attendanceService->punch((int) $validatedData['employee_id'], $companyId);

        return ApiResponse::success(['action' => $result['status']], $result['message']);
    }
}