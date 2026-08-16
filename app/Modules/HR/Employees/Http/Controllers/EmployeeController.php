<?php
// Path: app/Modules/HR/Employees/Http/Controllers/EmployeeController.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\HR\Employees\Application\EmployeeService;
use App\Modules\HR\Employees\Domain\EmployeeRepositoryInterface;
use App\Modules\HR\Employees\Http\Requests\StoreEmployeeRequest;

/**
 * Enterprise API Controller: Employees
 */
class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;
    protected EmployeeRepositoryInterface $employeeRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        EmployeeService $employeeService,
        EmployeeRepositoryInterface $employeeRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->employeeService = $employeeService;
        $this->employeeRepo = $employeeRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('hr', 'employees', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->employeeRepo->setTenantId($companyId);

        // Can be replaced with Pagination
        $employees = $this->employeeRepo->all();

        return ApiResponse::success($employees);
    }

    public function store(Request $request, StoreEmployeeRequest $validator): JsonResponse
    {
        $this->gate->authorize('hr', 'employees', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $employee = $this->employeeService->createEmployee($validatedData, $companyId);

        return ApiResponse::created($employee->toArray(), 'Employee successfully registered.');
    }
}