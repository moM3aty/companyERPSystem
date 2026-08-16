<?php
// Path: app/Modules/POS/Http/Controllers/PosController.php

declare(strict_types=1);

namespace App\Modules\POS\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\POS\Application\PosService;
use App\Modules\POS\Orders\Http\Requests\StorePosOrderRequest;

/**
 * Enterprise API Controller: POS Orders
 * واجهة الكاشير الأساسية لضرب المبيعات بسرعة عالية.
 */
class PosController extends Controller
{
    protected PosService $posService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PosService $posService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->posService = $posService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * استلام عملية بيع من الكاشير وتسجيلها فوراً.
     */
    public function storeOrder(Request $request, StorePosOrderRequest $validator): JsonResponse
    {
        $this->gate->authorize('pos', 'orders', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $orderId = $this->posService->createOrder($validatedData, $companyId, $userId);

        return ApiResponse::created(['order_id' => $orderId], 'POS Order completed and integrated successfully.');
    }
}