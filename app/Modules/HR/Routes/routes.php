<?php
// Path: app/Modules/HR/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\HR\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;

// Controllers
use App\Modules\HR\Controllers\DashboardController;
use App\Modules\HR\Employees\Http\Controllers\EmployeeController;
use App\Modules\HR\Contracts\Http\Controllers\ContractController;
use App\Modules\HR\Leaves\Http\Controllers\LeaveController;
use App\Modules\HR\Attendance\Http\Controllers\AttendanceController;
use App\Modules\HR\Recruitment\Http\Controllers\RecruitmentController;
use App\Modules\HR\Performance\Http\Controllers\PerformanceController;
use App\Modules\HR\Training\Http\Controllers\TrainingController;
use App\Modules\HR\EmployeeSelfService\Http\Controllers\EssLeaveController;
use App\Modules\HR\EmployeeSelfService\Http\Controllers\EssPayslipController;
use App\Modules\HR\EmployeeSelfService\Http\Controllers\EssExpenseClaimController;
use App\Modules\HR\Onboarding\Http\Controllers\OnboardingController;

/**
 * Enterprise HR & ESS Routes
 */
return static function (Router $router): void {
    
    // 1. HR Admin Routes
    $hr = new RouteGroup($router, ['prefix' => 'api/v1/hr', 'middleware' => ['api', 'auth', 'tenant']]);
    $hr->group(function (RouteGroup $group) {
        $group->get('/dashboard', [DashboardController::class, 'index']);
        
        $group->get('/employees', [EmployeeController::class, 'index']);
        $group->post('/employees', [EmployeeController::class, 'store']);
        
        $group->post('/contracts', [ContractController::class, 'store']);
        $group->post('/leaves', [LeaveController::class, 'store']);
        $group->post('/leaves/{id}/approve', [LeaveController::class, 'approve']);
        
        $group->post('/attendance/punch', [AttendanceController::class, 'punch']);
        
        $group->post('/recruitment/jobs', [RecruitmentController::class, 'storeJobOpening']);
        
        $group->post('/performance/appraisals', [PerformanceController::class, 'store']);
        $group->post('/training/programs', [TrainingController::class, 'store']);
        
        $group->post('/onboarding/tasks/{id}/complete', [OnboardingController::class, 'completeTask']);
    });
    
    // Public Recruitment Route
    $router->post('/api/v1/careers/apply', [RecruitmentController::class, 'apply'])->middleware(['api']);

    // 2. Employee Self Service (ESS) Routes - Scoped strictly to the logged-in employee
    $ess = new RouteGroup($router, ['prefix' => 'api/v1/ess', 'middleware' => ['api', 'auth', 'tenant']]);
    $ess->group(function (RouteGroup $group) {
        $group->get('/leaves/balance', [EssLeaveController::class, 'getMyBalance']);
        $group->get('/payslips', [EssPayslipController::class, 'getMyPayslips']);
        $group->post('/expenses', [EssExpenseClaimController::class, 'store']);
    });
};