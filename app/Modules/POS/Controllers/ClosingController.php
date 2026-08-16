<?php
// Path: app/Modules/POS/Controllers/ClosingController.php

declare(strict_types=1);

namespace App\Modules\POS\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\POS\Services\POSClosingService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class ClosingController extends Controller
{
    protected POSClosingService $closingService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        POSClosingService $closingService, 
        TenantContext $tenant, 
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->closingService = $closingService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function closeShift(Request $request, int $shiftId): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $actualAmount = (float)($data['actual_amount'] ?? 0);
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $report = $this->closingService->generateZReport($shiftId, $actualAmount, $companyId, $userId);

        return ApiResponse::success($report, 'POS Shift closed successfully and Z-Report generated.');
    }
}