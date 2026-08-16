<?php
// Path: app/Modules/Treasury/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Controller: Treasury Dashboard
 * يجمع البيانات التحليلية الرئيسية الخاصة بصناديق الشركة وحساباتها البنكية.
 */
class DashboardController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(Gate $gate, TenantContext $tenant)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('treasury', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        // في النظام الفعلي سيتم سحب البيانات من الـ CashForecastService أو الـ Metrics
        $data = [
            'total_cash'       => 45000.00,
            'total_bank'       => 1250500.00,
            'pending_receipts' => 5,
            'pending_payments' => 12,
        ];

        return ApiResponse::success($data, 'Treasury dashboard metrics retrieved successfully.');
    }
}