<?php
// Path: app/Modules/Manufacturing/Services/MaterialPlanningService.php
declare(strict_types=1);

namespace App\Modules\Manufacturing\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

class MaterialPlanningService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    /**
     * تشغيل تخطيط المواد (MRP) لأمر إنتاج محدد لتحديد العجز الفعلي للمواد الخام.
     */
    public function runMRPForOrder(int $productionOrderId, int $companyId): array
    {
        return $this->transaction->execute(function () use ($productionOrderId, $companyId) {
            
            // 1. حساب المواد المطلوبة بناءً على الـ BOM والكمية المراد إنتاجها
            $sql = "
                SELECT bi.component_product_id as raw_material_id, 
                       (bi.quantity * po.planned_quantity) as total_required,
                       COALESCE(s.quantity, 0) as available_stock
                FROM manufacturing_production_orders po
                JOIN manufacturing_boms b ON po.product_id = b.product_id
                JOIN manufacturing_bom_items bi ON b.id = bi.bom_id
                LEFT JOIN inventory_stocks s ON bi.component_product_id = s.product_id
                WHERE po.id = ? AND po.company_id = ? AND b.is_active = 1
            ";

            $requirements = $this->db->connection()->select($sql, [$productionOrderId, $companyId]);
            $shortages = [];

            // 2. تصفية العجز وتسجيله
            foreach ($requirements as $req) {
                $netReq = (float)$req['total_required'] - (float)$req['available_stock'];
                
                if ($netReq > 0) {
                    $this->db->connection()->insert(
                        "INSERT INTO manufacturing_material_requirements 
                        (company_id, production_order_id, raw_material_id, required_quantity, available_stock, net_requirement, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)",
                        [$companyId, $productionOrderId, $req['raw_material_id'], $req['total_required'], $req['available_stock'], $netReq, date('Y-m-d H:i:s')]
                    );
                    
                    $shortages[] = [
                        'raw_material_id' => $req['raw_material_id'],
                        'net_requirement' => $netReq
                    ];
                }
            }

            return $shortages;
        });
    }
}