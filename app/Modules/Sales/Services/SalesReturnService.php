<?php
// Path: app/Modules/Sales/Services/SalesReturnService.php

declare(strict_types=1);

namespace App\Modules\Sales\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;

class SalesReturnService
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

    public function processReturn(array $headerData, array $items, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($headerData, $items, $companyId, $userId) {
            
            $returnNo = 'RMA-' . date('ymd') . '-' . rand(100, 999);

            $this->db->connection()->insert(
                "INSERT INTO sales_returns (company_id, return_no, customer_id, invoice_id, return_date, status, received_by, created_at) 
                 VALUES (?, ?, ?, ?, ?, 'received', ?, ?)",
                [$companyId, $returnNo, $headerData['customer_id'], $headerData['invoice_id'] ?? null, $headerData['return_date'], $userId, date('Y-m-d H:i:s')]
            );
            
            $returnId = (int) $this->db->connection()->lastInsertId();

            foreach ($items as $item) {
                $this->db->connection()->insert(
                    "INSERT INTO sales_return_items (sales_return_id, product_id, quantity, condition_status) VALUES (?, ?, ?, ?)",
                    [$returnId, $item['product_id'], $item['quantity'], $item['condition'] ?? 'good']
                );

                if (($item['condition'] ?? 'good') === 'good') {
                    $this->stockService->recordMovement(
                        (int) $item['product_id'],
                        (int) $item['warehouse_id'],
                        (float) $item['quantity'],
                        StockMovementType::IN,
                        'sales_return',
                        $returnId,
                        $companyId,
                        $userId,
                        0.0, 
                        "Customer Return #{$returnNo}"
                    );
                }
            }

            return $returnId;
        });
    }
}