<?php
// Path: app/Modules/Inventory/MRP/Http/Controllers/MrpController.php

declare(strict_types=1);

namespace App\Modules\Inventory\MRP\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Inventory\MRP\Application\MrpEngine;

/**
 * Enterprise API Controller: Material Requirements Planning (MRP)
 * يتم استدعاؤه لتشغيل خوارزميات الـ MRP وتقديم توصيات الشراء والإنتاج آلياً.
 */
class MrpController extends Controller
{
    protected MrpEngine $mrpEngine;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(MrpEngine $mrpEngine, Gate $gate, TenantContext $tenant)
    {
        $this->mrpEngine = $mrpEngine;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function executeRun(Request $request): JsonResponse
    {
        $this->gate->authorize('inventory', 'mrp', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $recommendationsCount = $this->mrpEngine->run($companyId);

        return ApiResponse::success(
            ['recommendations_generated' => $recommendationsCount], 
            "MRP Run completed successfully. {$recommendationsCount} recommendations have been generated based on reorder rules and open demand."
        );
    }
}