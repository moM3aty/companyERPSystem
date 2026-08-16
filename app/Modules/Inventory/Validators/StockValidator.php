<?php
// Path: app/Modules/Inventory/Validators/StockValidator.php

declare(strict_types=1);

namespace App\Modules\Inventory\Validators;

use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Exceptions\NegativeStockException;

/**
 * Enterprise Validator: Stock Validation
 * يفحص الأرصدة المتاحة قبل السماح بعمليات المبيعات أو الصرف لمنع الأرصدة السالبة.
 */
class StockValidator
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @throws NegativeStockException
     */
    public function validateAvailability(int $productId, int $warehouseId, float $requestedQty, int $companyId): void
    {
        $stock = $this->db->connection()->selectOne(
            "SELECT (quantity - reserved_quantity) as available FROM inventory_stocks WHERE product_id = ? AND warehouse_id = ? AND company_id = ?",
            [$productId, $warehouseId, $companyId]
        );

        $available = $stock ? (float) $stock['available'] : 0.0;

        // التحقق من إعدادات الشركة إذا كانت تسمح بالرصيد السالب (افتراضياً: لا)
        $allowNegative = false; // يتم جلبها من System Settings في النظام الفعلي

        if (!$allowNegative && $requestedQty > $available) {
            throw new NegativeStockException($productId, $warehouseId, $requestedQty, $available);
        }
    }
}