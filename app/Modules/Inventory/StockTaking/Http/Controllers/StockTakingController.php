<?php
// Path: app/Modules/Inventory/StockTaking/Http/Controllers/StockTakingController.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\StockTaking\Application\StockTakingService;
use App\Modules\Inventory\StockTaking\Http\Requests\StoreStockCountRequest;

class StockTakingController extends Controller
{
    protected StockTakingService $stockTakingService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        StockTakingService $stockTakingService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->stockTakingService = $stockTakingService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreStockCountRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'stock_taking', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'warehouse_id' => $validatedData['warehouse_id'],
            'count_date'   => $validatedData['count_date'],
        ];

        $countId = $this->stockTakingService->processStockCount($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['stock_count_id' => $countId], 'Physical inventory count processed and system balances adjusted.');
    }
}