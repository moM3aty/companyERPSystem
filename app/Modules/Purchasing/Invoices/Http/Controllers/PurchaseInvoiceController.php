<?php
// Path: app/Modules/Purchasing/Invoices/Http/Controllers/PurchaseInvoiceController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\Invoices\Application\PurchaseInvoiceService;
use App\Modules\Purchasing\Invoices\Http\Requests\StorePurchaseInvoiceRequest;

/**
 * Enterprise API Controller: Purchase Invoices (Bills)
 */
class PurchaseInvoiceController extends Controller
{
    protected PurchaseInvoiceService $invoiceService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        PurchaseInvoiceService $invoiceService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->invoiceService = $invoiceService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StorePurchaseInvoiceRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'invoices', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'supplier_id'      => $validatedData['supplier_id'],
            'supplier_bill_no' => $validatedData['supplier_bill_no'],
            'invoice_date'     => $validatedData['invoice_date'],
            'due_date'         => $validatedData['due_date'],
            'currency_id'      => $validatedData['currency_id'],
        ];

        $invoiceId = $this->invoiceService->createInvoice($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['invoice_id' => $invoiceId], 'Purchase Invoice (Bill) securely calculated and saved.');
    }
}