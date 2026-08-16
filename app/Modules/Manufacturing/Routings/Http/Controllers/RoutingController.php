<?php
// Path: app/Modules/Manufacturing/Routings/Http/Controllers/RoutingController.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routings\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Manufacturing\Routings\Application\RoutingService;

class RoutingController extends Controller
{
    protected RoutingService $routingService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        RoutingService $routingService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->routingService = $routingService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('manufacturing', 'routings', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        $headerData = [
            'product_id' => $cleanData['product_id'] ?? 0,
            'code'       => $cleanData['code'] ?? '',
            'name'       => $cleanData['name'] ?? '',
        ];

        $routingId = $this->routingService->createRouting($headerData, $cleanData['steps'] ?? [], $companyId);

        return ApiResponse::created(['routing_id' => $routingId], 'Manufacturing Routing paths generated successfully.');
    }
}