<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Http/Controllers/ProductionOrderController.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\ApiError;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Manufacturing\ProductionOrders\Application\ProductionOrderService;
use App\Modules\Manufacturing\ProductionOrders\Http\Requests\StoreProductionOrderRequest;

/**
 * Enterprise API Controller: Production Orders
 * نقطة الدخول لإدارة أوامر الإنتاج. تربط بين التخطيط، وسحب المواد الخام، وتسجيل المنتج النهائي.
 */
class ProductionOrderController extends Controller
{
    protected ProductionOrderService $orderService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ProductionOrderService $orderService,
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

    /**
     * إنشاء أمر إنتاج جديد (مبني على BOM).
     */
    public function store(Request $request, StoreProductionOrderRequest $validator): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'production_orders', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $orderId = $this->orderService->createOrder($validatedData, $userId);

        return ApiResponse::created(['order_id' => $orderId], 'Production Order planned and material requirements calculated successfully.');
    }

    /**
     * إغلاق أمر الإنتاج وتسجيل الكمية المنتجة فعلياً لإطلاق القيود المحاسبية.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'production_orders', 'complete');

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $actualQty = (float) ($cleanData['actual_produced_quantity'] ?? 0);

        if ($actualQty <= 0) {
            return ApiError::error('Actual produced quantity must be strictly greater than zero.', 422);
        }

        $this->orderService->completeOrder($id, $actualQty);

        return ApiResponse::success(null, 'Production Order completed. Inventory updated and Journal Entries posted.');
    }
}