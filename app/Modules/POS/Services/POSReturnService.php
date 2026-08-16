<?php
// Path: app/Modules/POS/Services/POSReturnService.php

declare(strict_types=1);

namespace App\Modules\POS\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Exceptions\BusinessException;

class POSReturnService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected StockService $stockService;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, StockService $stockService)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->stockService = $stockService;
    }

    public function processReturn(int $posOrderId, string $reason, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($posOrderId, $reason, $companyId, $userId) {
            
            $order = $this->db->connection()->selectOne("SELECT * FROM pos_orders WHERE id = ? AND company_id = ?", [$posOrderId, $companyId]);
            if (!$order || $order['status'] === 'refunded') {
                throw new BusinessException("Order not found or already refunded.");
            }

            // 1. إنشاء المرتجع
            $this->db->connection()->insert(
                "INSERT INTO pos_returns (company_id, pos_order_id, return_amount, reason, status, created_by, created_at) 
                 VALUES (?, ?, ?, ?, 'refunded', ?, ?)",
                [$companyId, $posOrderId, $order['grand_total'], $reason, $userId, date('Y-m-d H:i:s')]
            );
            $returnId = (int) $this->db->connection()->lastInsertId();

            // 2. تحديث حالة الطلب
            $this->db->connection()->update("UPDATE pos_orders SET status = 'refunded' WHERE id = ?", [$posOrderId]);

            // 3. إعادة المخزون
            $items = $this->db->connection()->select("SELECT * FROM pos_order_items WHERE order_id = ?", [$posOrderId]);
            foreach ($items as $item) {
                $this->stockService->recordMovement(
                    (int)$item['product_id'], 1, (float)$item['quantity'], 
                    StockMovementType::IN, 'pos_return', $returnId, 
                    $companyId, $userId, 0.0, "POS Return for Order #{$order['order_number']}"
                );
            }

            return $returnId;
        });
    }
}