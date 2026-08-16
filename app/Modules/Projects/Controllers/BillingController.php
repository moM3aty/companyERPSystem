<?php
// Path: app/Modules/Projects/Controllers/BillingController.php

declare(strict_types=1);

namespace App\Modules\Projects\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Projects\Services\ProjectBillingService;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;

class BillingController extends Controller
{
    protected ProjectBillingService $billingService;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ProjectBillingService $billingService, 
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->billingService = $billingService;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function billTimesheets(Request $request, int $projectId): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;
        $hourlyRate = (float)($data['hourly_rate'] ?? 0);

        $invoiceId = $this->billingService->generateInvoiceFromTimesheets($projectId, $hourlyRate, $companyId);

        return ApiResponse::created(['project_invoice_id' => $invoiceId], 'Project invoice generated successfully based on logged timesheets.');
    }
}