<?php
// Path: app/Modules/HR/EmployeeSelfService/Http/Controllers/EssLeaveController.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Modules\HR\EmployeeSelfService\Application\LeaveBalanceService;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise API Controller: Employee Self Service (ESS) - Leaves
 * جزء من بوابة الموظفين (Employee Portal) يتيح للموظف الاستعلام عن رصيده دون الحاجة لصلاحيات HR.
 */
class EssLeaveController extends Controller
{
    protected LeaveBalanceService $balanceService;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(LeaveBalanceService $balanceService, TenantContext $tenant, AuthManager $auth)
    {
        $this->balanceService = $balanceService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        
        $this->middleware(['api', 'auth', 'tenant']); // لا يحتاج Gate لأنه متاح لأي موظف لنفسه
    }

    public function getMyBalance(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $user = $this->auth->user();

        if (!$user || !$user->employeeId) {
            throw new AuthorizationException("Your user account is not linked to an employee profile.");
        }

        $year = (int) $request->query('year', date('Y'));

        $balance = $this->balanceService->getAnnualLeaveBalance($user->employeeId, $companyId, $year);

        return ApiResponse::success($balance, 'Leave balance retrieved successfully.');
    }
}