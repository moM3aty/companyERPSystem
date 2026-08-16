<?php
// Path: app/Modules/Treasury/Payments/Http/Controllers/PaymentVoucherController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Treasury\Payments\Application\PaymentVoucherService;
use App\Modules\Treasury\Payments\Http\Requests\StorePaymentVoucherRequest;

/**
 * Enterprise API Controller: Payment Vouchers
 */
class PaymentVoucherController extends Controller
{
    protected PaymentVoucherService $voucherService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PaymentVoucherService $voucherService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->voucherService = $voucherService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StorePaymentVoucherRequest $validator): JsonResponse
    {
        $this->gate->authorize('treasury', 'payment_vouchers', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $voucher = $this->voucherService->createAndPostVoucher($validatedData, $companyId, $userId);

        return ApiResponse::created($voucher, 'Payment Voucher successfully created and posted to the General Ledger.');
    }
}