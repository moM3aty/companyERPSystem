<?php
// Path: app/Modules/Payroll/PayrollRuns/Http/Controllers/PayrollController.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Payroll\PayrollRuns\Application\PayrollService;
use App\Modules\Payroll\PayrollRuns\Http\Requests\StorePayrollRunRequest;

/**
 * Enterprise API Controller: Payroll
 * نقطة الدخول لإدارة الرواتب (توليد مسير الرواتب الشهري).
 */
class PayrollController extends Controller
{
    protected PayrollService $payrollService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PayrollService $payrollService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->payrollService = $payrollService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * تشغيل مسير الرواتب وتوليد القسائم.
     */
    public function store(Request $request, StorePayrollRunRequest $validator): JsonResponse
    {
        $this->gate->authorize('payroll', 'runs', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $run = $this->payrollService->processPayroll($validatedData['run_period'], $companyId, $userId);

        return ApiResponse::created($run, 'Payroll run processed successfully and payslips have been generated.');
    }
}