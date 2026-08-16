<?php
// Path: app/Modules/FixedAssets/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Fixed Assets Dashboard
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
        $this->gate->authorize('fixed_assets', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        $stats = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as active_count, SUM(purchase_value) as total_purchase, SUM(net_book_value) as total_nbv 
             FROM fixed_assets WHERE company_id = ? AND status = 'active'",
            [$companyId]
        );

        $data = [
            'active_assets_count'     => (int) ($stats['active_count'] ?? 0),
            'total_purchase_value'    => (float) ($stats['total_purchase'] ?? 0.0),
            'total_net_book_value'    => (float) ($stats['total_nbv'] ?? 0.0),
            'accumulated_depreciation'=> (float) (($stats['total_purchase'] ?? 0) - ($stats['total_nbv'] ?? 0)),
        ];

        return ApiResponse::success($data, 'Fixed Assets dashboard metrics retrieved.');
    }
}