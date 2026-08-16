<?php
// Path: app/Modules/Sales/CustomerPayments/Http/Controllers/CustomerPaymentController.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\CustomerPayments\Application\CustomerPaymentService;
use App\Modules\Sales\CustomerPayments\Http\Requests\StoreInvoiceAllocationRequest;

/**
 * Enterprise API Controller: Customer Payments / Invoice Allocation
 * نقطة الدخول لإجراء التسويات المحاسبية بين سندات القبض وفواتير المبيعات (Settlement).
 */
class CustomerPaymentController extends Controller
{
    protected CustomerPaymentService $paymentService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        CustomerPaymentService $paymentService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->paymentService = $paymentService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function allocate(Request $request, StoreInvoiceAllocationRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'customer_payments', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $this->paymentService->allocateReceipt(
            (int) $validatedData['receipt_id'],
            $validatedData['allocations'],
            $userId
        );

        return ApiResponse::success(null, 'Receipt successfully allocated to the selected invoices. AR balances updated.');
    }
}