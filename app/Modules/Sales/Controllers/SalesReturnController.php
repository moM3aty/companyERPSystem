<?php
// Path: app/Modules/Sales/Controllers/SalesReturnController.php

declare(strict_types=1);

namespace App\Modules\Sales\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Sales\Services\SalesReturnService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class SalesReturnController extends Controller
{
    protected SalesReturnService $returnService;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        SalesReturnService $returnService, 
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

    public function store(Request $request): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $returnId = $this->returnService->processReturn($data, $data['items'] ?? [], $companyId, $userId);

        return ApiResponse::created(['sales_return_id' => $returnId], 'Sales return processed and stock updated.');
    }
}