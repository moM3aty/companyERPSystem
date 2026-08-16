<?php
// Path: app/Modules/Treasury/Receipts/Http/Controllers/ReceiptController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Treasury\Receipts\Application\ReceiptService;
use App\Modules\Treasury\Receipts\Http\Requests\StoreReceiptRequest;

/**
 * Enterprise API Controller: Receipts
 * نقطة الدخول لأمناء الصناديق والمحاسبين لإنشاء سندات القبض.
 */
class ReceiptController extends Controller
{
    protected ReceiptService $receiptService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ReceiptService $receiptService,
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

    /**
     * إنشاء سند قبض وترحيله كقيد محاسبي.
     *
     * @param Request $request
     * @param StoreReceiptRequest $validator
     * @return JsonResponse
     */
    public function store(Request $request, StoreReceiptRequest $validator): JsonResponse
    {
        $this->gate->authorize('treasury', 'receipts', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        
        // التوجيه للـ Application Service
        $receipt = $this->receiptService->createAndPostReceipt($validatedData, $companyId, $userId);

        return ApiResponse::created($receipt, 'Receipt successfully created and posted to the General Ledger.');
    }
}