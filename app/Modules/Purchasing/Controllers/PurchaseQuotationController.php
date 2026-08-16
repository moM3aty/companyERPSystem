<?php
// Path: app/Modules/Purchasing/Controllers/PurchaseQuotationController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Purchasing\Services\PurchaseQuotationService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;

class PurchaseQuotationController extends Controller
{
    protected PurchaseQuotationService $quotationService;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(
        PurchaseQuotationService $quotationService, 
        TenantContext $tenant, 
        AuthManager $auth
    ) {
        $this->quotationService = $quotationService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function award(Request $request, int $id): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $poId = $this->quotationService->awardQuotation($id, $companyId, $this->auth->user()->id);

        return ApiResponse::success(['purchase_order_id' => $poId], 'Quotation awarded successfully and PO generated.');
    }
}