<?php
// Path: app/Modules/Inventory/MRP/Application/MrpEngine.php

declare(strict_types=1);

namespace App\Modules\Inventory\MRP\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Material Requirements Planning (MRP) Engine
 * المحرك الاستراتيجي لسلاسل الإمداد. 
 * يقوم بتحليل الأرصدة الحالية، الطلبات المفتوحة (Sales/Production)، وقواعد الحد الأدنى 
 * لتوليد "توصيات شراء" أو "أوامر إنتاج" تلقائياً.
 */
class MrpEngine
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * تشغيل تخطيط الاحتياجات (MRP Run) لشركة معينة.
     * عادة يتم تشغيلها كـ Background Job ليلاً.
     *
     * @param int $companyId
     * @return int عدد التوصيات (Recommendations) التي تم إنشاؤها
     */
    public function run(int $companyId): int
    {
        $this->logger->info("Starting MRP Run for Company [{$companyId}]");
        $recommendationsCount = 0;
        $now = date('Y-m-d H:i:s');

        // 1. مسح التوصيات القديمة التي لم يتم تحويلها لأوامر فعلية لتجنب التكرار
        $this->db->connection()->delete(
            "DELETE FROM mrp_recommendations WHERE company_id = ? AND status = 'pending'",
            [$companyId]
        );

        // 2. جلب جميع قواعد إعادة الطلب النشطة
        $rules = $this->db->connection()->select(
            "SELECT * FROM inventory_reorder_rules WHERE company_id = ? AND is_active = 1",
            [$companyId]
        );

        foreach ($rules as $rule) {
            $productId = (int) $rule['product_id'];
            $warehouseId = (int) $rule['warehouse_id'];
            $minQty = (float) $rule['min_quantity'];
            $maxQty = (float) $rule['max_quantity'];

            // 3. حساب المخزون المتوقع (Projected Inventory)
            // = الرصيد الفعلي + المشتريات المفتوحة - المبيعات/التصنيع المحجوزة
            
            // الرصيد الفعلي
            $stockSql = "SELECT quantity FROM inventory_stocks WHERE product_id = ? AND warehouse_id = ? AND company_id = ?";
            $stock = $this->db->connection()->selectOne($stockSql, [$productId, $warehouseId, $companyId]);
            $actualQty = $stock ? (float) $stock['quantity'] : 0.0;

            // الكميات القادمة (أوامر شراء معتمدة لم تستلم بعد)
            $incomingSql = "SELECT SUM(quantity - received_quantity) as incoming FROM purchase_order_items poi
                            JOIN purchase_orders po ON poi.purchase_order_id = po.id
                            WHERE poi.product_id = ? AND po.status IN ('approved', 'sent')";
            $incoming = $this->db->connection()->selectOne($incomingSql, [$productId]);
            $incomingQty = $incoming ? (float) $incoming['incoming'] : 0.0;

            // الكميات المطلوبة للغير (أوامر مبيعات أو إنتاج لم تصرف بعد)
            $outgoingSql = "SELECT SUM(quantity - delivered_quantity) as outgoing FROM sales_order_items soi
                            JOIN sales_orders so ON soi.order_id = so.id
                            WHERE soi.product_id = ? AND so.status = 'approved'";
            $outgoing = $this->db->connection()->selectOne($outgoingSql, [$productId]);
            $outgoingQty = $outgoing ? (float) $outgoing['outgoing'] : 0.0;

            // المخزون المتوقع
            $projectedQty = $actualQty + $incomingQty - $outgoingQty;

            // 4. اتخاذ القرار (إذا كان المتوقع أقل من الحد الأدنى، نطلب كمية لتصل للحد الأقصى)
            if ($projectedQty <= $minQty) {
                $qtyToOrder = $maxQty - $projectedQty;
                
                // حفظ التوصية
                $this->db->connection()->insert(
                    "INSERT INTO mrp_recommendations 
                     (company_id, product_id, warehouse_id, recommended_quantity, type, status, created_at) 
                     VALUES (?, ?, ?, ?, 'purchase', 'pending', ?)",
                    [$companyId, $productId, $warehouseId, $qtyToOrder, $now]
                );

                $recommendationsCount++;
            }
        }

        $this->logger->info("MRP Run completed. Generated {$recommendationsCount} recommendations.");
        return $recommendationsCount;
    }
}