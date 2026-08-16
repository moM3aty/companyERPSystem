<?php
// Path: app/Modules/Manufacturing/BOM/Http/Controllers/BOMController.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Manufacturing\BOM\Application\BOMService;
use App\Modules\Manufacturing\BOM\Http\Requests\StoreBOMRequest;

/**
 * Enterprise API Controller: Bill of Materials (BOM)
 */
class BOMController extends Controller
{
    protected BOMService $bomService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        BOMService $bomService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->bomService = $bomService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreBOMRequest $validator): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'bom', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'product_id'     => $validatedData['product_id'],
            'code'           => $validatedData['code'],
            'name'           => $validatedData['name'],
            'batch_quantity' => $validatedData['batch_quantity'],
        ];

        $bomId = $this->bomService->createBOM($headerData, $validatedData['items'], $companyId);

        return ApiResponse::created(['bom_id' => $bomId], 'Bill of Materials registered successfully.');
    }
}