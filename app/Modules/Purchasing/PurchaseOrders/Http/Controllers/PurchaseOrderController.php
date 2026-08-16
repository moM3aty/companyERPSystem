<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Http/Controllers/PurchaseOrderController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\PurchaseOrders\Application\PurchaseOrderService;
use App\Modules\Purchasing\PurchaseOrders\Http\Requests\StorePurchaseOrderRequest;

/**
 * Enterprise API Controller: Purchase Orders
 */
class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $poService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PurchaseOrderService $poService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->poService = $poService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StorePurchaseOrderRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'purchase_orders', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'supplier_id'            => $validatedData['supplier_id'],
            'order_date'             => $validatedData['order_date'],
            'expected_delivery_date' => $validatedData['expected_delivery_date'] ?? null,
            'currency_id'            => $validatedData['currency_id'],
            'notes'                  => $validatedData['notes'] ?? null,
        ];

        $poId = $this->poService->createPurchaseOrder($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['purchase_order_id' => $poId], 'Purchase Order generated and calculated successfully.');
    }
}