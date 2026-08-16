<?php
// File 6: app/Modules/SupplyChain/Controllers/ReorderController.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\SupplyChain\Services\ReorderService;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;

class ReorderController extends Controller
{
    protected ReorderService $reorderService;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(ReorderService $reorderService, TenantContext $tenant, AuthManager $auth)
    {
        $this->reorderService = $reorderService;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function runReplenishment(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $itemsOrdered = $this->reorderService->generateReplenishmentRequests($companyId, $this->auth->user()->id);

        return ApiResponse::success(['items_replenished' => $itemsOrdered], "Replenishment engine executed. Draft Purchase Requests generated.");
    }
}