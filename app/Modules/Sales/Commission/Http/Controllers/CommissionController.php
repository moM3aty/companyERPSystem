<?php
// Path: app/Modules/Sales/Commission/Http/Controllers/CommissionController.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\ApiError;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Sales\Commission\Application\CommissionEngine;

/**
 * Enterprise API Controller: Sales Commission
 */
class CommissionController extends Controller
{
    protected CommissionEngine $commissionEngine;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(CommissionEngine $commissionEngine, Gate $gate, TenantContext $tenant)
    {
        $this->commissionEngine = $commissionEngine;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function calculate(Request $request): JsonResponse
    {
        $this->gate->authorize('sales', 'commissions', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $employeeId = (int) $request->post('employee_id');
        $startDate = $request->post('start_date');
        $endDate = $request->post('end_date');

        if (!$employeeId || !$startDate || !$endDate) {
            return ApiError::error("Employee ID, Start Date, and End Date are required.", 422);
        }

        $count = $this->commissionEngine->calculateCommissions($employeeId, $startDate, $endDate, $companyId);

        return ApiResponse::success(['calculated_records' => $count], "Commission calculation completed. Generated {$count} commission records.");
    }
}