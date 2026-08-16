<?php
// Path: app/Modules/Sales/Quotations/Http/Controllers/QuotationController.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\Quotations\Application\QuotationService;
use App\Modules\Sales\Quotations\Http\Requests\StoreQuotationRequest;

/**
 * Enterprise API Controller: Sales Quotations
 */
class QuotationController extends Controller
{
    protected QuotationService $quotationService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        QuotationService $quotationService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->quotationService = $quotationService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreQuotationRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'quotations', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'customer_id'    => $validatedData['customer_id'],
            'quotation_date' => $validatedData['quotation_date'],
            'valid_until'    => $validatedData['valid_until'],
            'currency_id'    => $validatedData['currency_id'],
        ];

        $quotationId = $this->quotationService->createQuotation($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['quotation_id' => $quotationId], 'Sales Quotation drafted securely.');
    }
}