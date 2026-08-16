<?php
// Path: app/Modules/Purchasing/Controllers/DashboardController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Purchasing Dashboard
 * يجمع المؤشرات الحيوية (KPIs) لموديول المشتريات.
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
        $this->gate->authorize('purchasing', 'dashboard', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;

        // جلب الإحصائيات (يفضل في الأنظمة الضخمة أن تأتي من Data Warehouse أو كاش)
        $activeSuppliers = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM suppliers WHERE company_id = ? AND is_active = 1", [$companyId]);
        $pendingPos = $this->db->connection()->selectOne("SELECT COUNT(id) as cnt FROM purchase_orders WHERE company_id = ? AND status = 'approved'", [$companyId]);
        
        // حساب إجمالي قيمة أوامر الشراء المفتوحة
        $openPoValue = $this->db->connection()->selectOne("SELECT SUM(grand_total) as total FROM purchase_orders WHERE company_id = ? AND status IN ('approved', 'sent')", [$companyId]);

        $data = [
            'active_suppliers'  => (int) ($activeSuppliers['cnt'] ?? 0),
            'pending_pos'       => (int) ($pendingPos['cnt'] ?? 0),
            'open_po_value'     => (float) ($openPoValue['total'] ?? 0.0),
        ];

        return ApiResponse::success($data, 'Purchasing Dashboard metrics retrieved.');
    }
}