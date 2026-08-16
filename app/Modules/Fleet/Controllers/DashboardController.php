<?php
// Path: app/Modules/Fleet/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Fleet\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Fleet Dashboard
 */
class DashboardController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;
    protected DatabaseManager $db;

    public function __construct(Gate $gate, TenantContext $tenant, DatabaseManager $db)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->db = $db;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('fleet', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $vehicles = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM fleet_vehicles WHERE company_id = ? AND status = 'active'", [$companyId]);
        $activeTrips = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM fleet_trips WHERE company_id = ? AND status = 'in_progress'", [$companyId]);

        $data = [
            'active_vehicles'     => (int) ($vehicles['cnt'] ?? 0),
            'in_transit_trips'    => (int) ($activeTrips['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Fleet dashboard metrics retrieved.');
    }
}