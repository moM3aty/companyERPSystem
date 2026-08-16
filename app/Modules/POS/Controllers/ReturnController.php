<?php
// Path: app/Modules/POS/Controllers/ReturnController.php

declare(strict_types=1);

namespace App\Modules\POS\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\POS\Services\POSReturnService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class ReturnController extends Controller
{
    protected POSReturnService $returnService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        POSReturnService $returnService, 
        TenantContext $tenant, 
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->returnService = $returnService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function refund(Request $request, int $orderId): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $returnId = $this->returnService->processReturn($orderId, $data['reason'] ?? '', $companyId, $this->auth->user()->id);

        return ApiResponse::created(['pos_return_id' => $returnId], 'POS order refunded and inventory restored.');
    }
}