<?php
// Path: app/Modules/Inventory/StockMovements/Http/Controllers/StockMovementController.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\Pagination;
use App\Core\Api\Filter;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Inventory\StockMovements\Domain\StockMovementRepositoryInterface;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Stock Movements (Item Ledger)
 * عرض كارتة الصنف (Item Ledger) لتدقيق الحركات.
 * مُحسن لدعم نمط الـ CQRS للتقارير الثقيلة عبر استخدام استعلامات مباشرة للـ Read-Model
 * مع دعم فلاتر التواريخ وحساب الرصيد الافتتاحي (Opening Balance).
 */
class StockMovementController extends Controller
{
    protected StockMovementRepositoryInterface $movementRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected DatabaseManager $db;

    public function __construct(
        StockMovementRepositoryInterface $movementRepo, 
        Gate $gate, 
        TenantContext $tenant,
        DatabaseManager $db
    ) {
        $this->movementRepo = $movementRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->db = $db;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * استعراض كارتة الصنف مع حساب الرصيد الافتتاحي (Opening Balance).
     */
    public function history(Request $request, int $productId, int $warehouseId): JsonResponse
    {
        $this->gate->authorize('inventory', 'stock_movements', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        // استخراج الإعدادات من الطلب
        $pagination = Pagination::extract($request, 50, 500);
        $filters = Filter::extract($request, ['from_date', 'to_date', 'movement_type']);
        
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;
        $movementType = $filters['movement_type'] ?? null;

        $offset = ($pagination['page'] - 1) * $pagination['per_page'];

        // 1. حساب الرصيد الافتتاحي (إذا تم تمرير تاريخ بداية، نحسب الرصيد قبله)
        $openingBalance = 0.0;
        if ($fromDate && $pagination['page'] === 1) {
            $obQuery = $this->db->connection()->getPdo()->prepare("
                SELECT balance_after FROM inventory_stock_movements 
                WHERE product_id = :product_id 
                  AND warehouse_id = :warehouse_id 
                  AND company_id = :company_id 
                  AND created_at < :from_date
                ORDER BY created_at DESC, id DESC LIMIT 1
            ");
            $obQuery->execute([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'company_id' => $companyId,
                'from_date' => $fromDate . ' 00:00:00'
            ]);
            $obResult = $obQuery->fetch(\PDO::FETCH_ASSOC);
            if ($obResult) {
                $openingBalance = (float) $obResult['balance_after'];
            }
        }

        // 2. بناء استعلام الحركات الفعلي مع الفلاتر
        $sql = "SELECT * FROM inventory_stock_movements 
                WHERE product_id = :product_id 
                  AND warehouse_id = :warehouse_id 
                  AND company_id = :company_id";
        
        if ($fromDate) $sql .= " AND created_at >= :from_date";
        if ($toDate) $sql .= " AND created_at <= :to_date";
        if ($movementType) $sql .= " AND movement_type = :movement_type";
        
        $sql .= " ORDER BY created_at ASC, id ASC LIMIT :limit OFFSET :offset";

        $query = $this->db->connection()->getPdo()->prepare($sql);
        
        $query->bindValue(':product_id', $productId, \PDO::PARAM_INT);
        $query->bindValue(':warehouse_id', $warehouseId, \PDO::PARAM_INT);
        $query->bindValue(':company_id', $companyId, \PDO::PARAM_INT);
        $query->bindValue(':limit', $pagination['per_page'], \PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        if ($fromDate) $query->bindValue(':from_date', $fromDate . ' 00:00:00');
        if ($toDate) $query->bindValue(':to_date', $toDate . ' 23:59:59');
        if ($movementType) $query->bindValue(':movement_type', $movementType);

        $query->execute();
        $movements = $query->fetchAll(\PDO::FETCH_ASSOC);

        // 3. حساب إجمالي السجلات للـ Pagination
        $countSql = "SELECT COUNT(*) FROM inventory_stock_movements 
                     WHERE product_id = :product_id 
                       AND warehouse_id = :warehouse_id 
                       AND company_id = :company_id";
        if ($fromDate) $countSql .= " AND created_at >= :from_date";
        if ($toDate) $countSql .= " AND created_at <= :to_date";
        if ($movementType) $countSql .= " AND movement_type = :movement_type";

        $countQuery = $this->db->connection()->getPdo()->prepare($countSql);
        $countParams = ['product_id' => $productId, 'warehouse_id' => $warehouseId, 'company_id' => $companyId];
        if ($fromDate) $countParams['from_date'] = $fromDate . ' 00:00:00';
        if ($toDate) $countParams['to_date'] = $toDate . ' 23:59:59';
        if ($movementType) $countParams['movement_type'] = $movementType;

        $countQuery->execute($countParams);
        $totalRecords = (int) $countQuery->fetchColumn();

        // 4. تجميع البيانات
        $meta = [
            'opening_balance' => $openingBalance,
            'pagination' => [
                'total'        => $totalRecords,
                'per_page'     => $pagination['per_page'],
                'current_page' => $pagination['page'],
                'last_page'    => (int) ceil($totalRecords / $pagination['per_page'])
            ]
        ];

        return ApiResponse::success($movements, 'Item ledger retrieved successfully.', 200, $meta);
    }

    /**
     * جلب الحركات المرتبطة بمستند معين (مثل عرض جميع حركات الصرف لفاتورة معينة).
     */
    public function byReference(Request $request, string $referenceType, int $referenceId): JsonResponse
    {
        $this->gate->authorize('inventory', 'stock_movements', 'view');
        $companyId = $this->tenant->requireTenant()->companyId;

        $movements = $this->movementRepo->getByReference($referenceType, $referenceId, $companyId);
        $data = array_map(fn($m) => $m->toArray(), $movements);

        return ApiResponse::success($data, 'Movements by reference retrieved successfully.');
    }
}