<?php
// Path: app/Modules/Inventory/Services/InventoryValuationService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Service: Inventory Valuation
 * מחשב قيمة المخزون الدقيقة (Inventory Valuation) لاستخدامها في الميزانية العمومية.
 * يعتمد على استخراج القيمة عبر ضرب (الكمية × التكلفة المتوسطة الحالية).
 */
class InventoryValuationService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * حساب القيمة الإجمالية للمخزون الحالي لكل المستودعات في الشركة.
     *
     * @param int $companyId
     * @return array مصفوفة تحتوي على التفاصيل والمجموع الكلي
     */
    public function getCurrentValuation(int $companyId): array
    {
        $sql = "
            SELECT 
                w.name as warehouse_name,
                SUM(s.quantity * s.average_cost) as total_value
            FROM inventory_stocks s
            JOIN warehouses w ON s.warehouse_id = w.id
            WHERE s.company_id = ? AND s.quantity > 0
            GROUP BY w.id, w.name
            ORDER BY total_value DESC
        ";

        $rows = $this->db->connection()->select($sql, [$companyId]);

        $grandTotal = 0.0;
        $details = [];

        foreach ($rows as $row) {
            $value = (float) $row['total_value'];
            $grandTotal += $value;
            
            $details[] = [
                'warehouse' => $row['warehouse_name'],
                'value'     => round($value, 2)
            ];
        }

        return [
            'total_inventory_value' => round($grandTotal, 2),
            'breakdown_by_warehouse'=> $details,
            'valuation_date'        => date('Y-m-d H:i:s')
        ];
    }
}