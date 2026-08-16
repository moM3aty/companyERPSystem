<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Http/Controllers/GoodsReceiptController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\GoodsReceipts\Application\GoodsReceiptService;
use App\Modules\Purchasing\GoodsReceipts\Http\Requests\StoreGoodsReceiptRequest;

class GoodsReceiptController extends Controller
{
    protected GoodsReceiptService $receiptService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        GoodsReceiptService $receiptService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->receiptService = $receiptService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreGoodsReceiptRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'goods_receipts', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'supplier_id'       => $validatedData['supplier_id'],
            'purchase_order_id' => $validatedData['purchase_order_id'] ?? null,
            'receipt_date'      => $validatedData['receipt_date'],
            'reference_doc'     => $validatedData['reference_doc'] ?? null,
        ];

        $receiptId = $this->receiptService->processReceipt($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['receipt_id' => $receiptId], 'Goods Receipt Note processed and inventory updated successfully.');
    }
}