<?php
// Path: app/Modules/Maintenance/History/Http/Controllers/MaintenanceHistoryController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\History\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

class MaintenanceHistoryController extends Controller
{
    protected DatabaseManager $db;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, Gate $gate, TenantContext $tenant)
    {
        $this->db = $db;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function show(int $assetId): JsonResponse
    {
        $this->gate->authorize('maintenance', 'history', 'view');
        $companyId = $this->tenant->requireTenant()->companyId;

        // جلب تاريخ الصيانة (أوامر العمل المكتملة) للأصل المحدد
        $sql = "SELECT work_order_number, title, completed_at, actual_cost 
                FROM maintenance_work_orders 
                WHERE asset_id = ? AND company_id = ? AND status = 'completed' 
                ORDER BY completed_at DESC";

        $history = $this->db->connection()->select($sql, [$assetId, $companyId]);

        return ApiResponse::success(['asset_id' => $assetId, 'maintenance_history' => $history]);
    }
}