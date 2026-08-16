<?php
// Path: app/Modules/Inventory/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Inventory Dashboard
 * يعرض إحصائيات المخازن والأرصدة المنخفضة وحركات النقل.
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
        $this->gate->authorize('inventory', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        // إجمالي قيمة المخزون الحالي
        $valuation = $this->db->connection()->selectOne(
            "SELECT SUM(quantity * average_cost) as total_value FROM inventory_stocks WHERE company_id = ? AND quantity > 0",
            [$companyId]
        );

        // عدد الأصناف الفعالة
        $activeProducts = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as cnt FROM products WHERE company_id = ? AND is_active = 1",
            [$companyId]
        );

        // التنبيهات: منتجات تجاوزت الحد الأدنى (Reorder Level)
        $lowStock = $this->db->connection()->selectOne(
            "SELECT COUNT(s.id) as cnt FROM inventory_stocks s
             JOIN inventory_reorder_rules r ON s.product_id = r.product_id AND s.warehouse_id = r.warehouse_id
             WHERE s.company_id = ? AND s.quantity <= r.min_quantity",
            [$companyId]
        );

        $data = [
            'total_inventory_value' => round((float) ($valuation['total_value'] ?? 0), 2),
            'active_products_count' => (int) ($activeProducts['cnt'] ?? 0),
            'low_stock_alerts'      => (int) ($lowStock['cnt'] ?? 0),
        ];

        return ApiResponse::success($data, 'Inventory dashboard metrics retrieved.');
    }
}