<?php
// Path: app/Modules/Sales/Deliveries/Http/Controllers/DeliveryNoteController.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\Deliveries\Application\DeliveryNoteService;
use App\Modules\Sales\Deliveries\Http\Requests\StoreDeliveryNoteRequest;

class DeliveryNoteController extends Controller
{
    protected DeliveryNoteService $deliveryService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        DeliveryNoteService $deliveryService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->deliveryService = $deliveryService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreDeliveryNoteRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'deliveries', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'customer_id'    => $validatedData['customer_id'],
            'sales_order_id' => $validatedData['sales_order_id'] ?? null,
            'delivery_date'  => $validatedData['delivery_date'],
        ];

        $deliveryId = $this->deliveryService->processDelivery($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['delivery_id' => $deliveryId], 'Delivery Note processed and stock deducted successfully.');
    }
}