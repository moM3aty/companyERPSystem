<?php
// Path: app/Modules/Sales/Invoices/Http/Controllers/SalesInvoiceController.php

declare(strict_types=1);

namespace App\Modules\Sales\Invoices\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Core\Sales\Services\InvoiceService;
use App\Modules\Sales\Invoices\Http\Requests\StoreSalesInvoiceRequest;

/**
 * Enterprise API Controller: Sales Invoices
 * نقطة الدخول لإنشاء فواتير المبيعات. تعتمد على Engine معقد لضمان دقة العمليات المحاسبية.
 */
class SalesInvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        InvoiceService $invoiceService,
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

    /**
     * إنشاء فاتورة مبيعات جديدة.
     */
    public function store(Request $request, StoreSalesInvoiceRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'invoices', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        // فصل الترويسة عن الأصناف
        $headerData = [
            'customer_id'  => $validatedData['customer_id'],
            'invoice_date' => $validatedData['invoice_date'],
            'due_date'     => $validatedData['due_date'],
            'currency_id'  => $validatedData['currency_id'],
        ];

        // Service تتولى عمليات الضرب والجمع لضمان عدم التلاعب من الـ Frontend
        $invoiceId = $this->invoiceService->createInvoice($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['invoice_id' => $invoiceId], 'Sales Invoice created securely and calculated successfully.');
    }
}