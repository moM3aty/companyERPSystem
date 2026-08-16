<?php
// Path: app/Modules/Inventory/Transfers/Http/Controllers/StockTransferController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\Transfers\Application\StockTransferService;
use App\Modules\Inventory\Transfers\Http\Requests\StoreStockTransferRequest;

/**
 * Enterprise API Controller: Stock Transfers
 */
class StockTransferController extends Controller
{
    protected StockTransferService $transferService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        StockTransferService $transferService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->transferService = $transferService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreStockTransferRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'transfers', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'from_warehouse_id' => $validatedData['from_warehouse_id'],
            'to_warehouse_id'   => $validatedData['to_warehouse_id'],
            'transfer_date'     => $validatedData['transfer_date'],
        ];

        $transferId = $this->transferService->executeTransfer($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['transfer_id' => $transferId], 'Stock Transfer executed successfully between warehouses.');
    }
}