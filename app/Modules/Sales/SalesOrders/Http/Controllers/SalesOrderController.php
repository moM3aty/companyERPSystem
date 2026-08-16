<?php
// Path: app/Modules/Sales/SalesOrders/Http/Controllers/SalesOrderController.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\SalesOrders\Application\SalesOrderService;
use App\Modules\Sales\SalesOrders\Http\Requests\StoreSalesOrderRequest;

class SalesOrderController extends Controller
{
    protected SalesOrderService $orderService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        SalesOrderService $orderService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->orderService = $orderService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreSalesOrderRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'sales_orders', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'customer_id'   => $validatedData['customer_id'],
            'quotation_id'  => $validatedData['quotation_id'] ?? null,
            'order_date'    => $validatedData['order_date'],
            'delivery_date' => $validatedData['delivery_date'] ?? null,
            'currency_id'   => $validatedData['currency_id'],
        ];

        $orderId = $this->orderService->createOrder($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['sales_order_id' => $orderId], 'Sales Order confirmed securely.');
    }
}