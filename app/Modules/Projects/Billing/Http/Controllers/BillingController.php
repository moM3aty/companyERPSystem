<?php
// Path: app/Modules/Projects/Billing/Http/Controllers/BillingController.php

declare(strict_types=1);

namespace App\Modules\Projects\Billing\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Modules\Projects\Billing\Application\ProjectBillingService;

/**
 * Enterprise API Controller: Project Billing
 */
class BillingController extends Controller
{
    protected ProjectBillingService $billingService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(
        ProjectBillingService $billingService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth
    ) {
        $this->billingService = $billingService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function generateInvoice(Request $request, int $projectId): JsonResponse
    {
        $this->gate->authorize('projects', 'billing', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $invoiceId = $this->billingService->generateInvoiceForProject($projectId, $companyId, $userId);

        return ApiResponse::created(['sales_invoice_id' => $invoiceId], 'Project billed successfully and Sales Invoice generated.');
    }
}