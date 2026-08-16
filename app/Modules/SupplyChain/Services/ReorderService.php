<?php
// Path: app/Modules/SupplyChain/Services/ReorderService.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

class ReorderService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    public function generateReplenishmentRequests(int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($companyId, $userId) {
            $sql = "
                SELECT r.product_id, r.warehouse_id, r.min_quantity, r.max_quantity, 
                       COALESCE(s.quantity, 0) as current_stock
                FROM supply_chain_reorder_rules r
                LEFT JOIN inventory_stocks s ON r.product_id = s.product_id AND r.warehouse_id = s.warehouse_id
                WHERE r.company_id = ? AND r.is_active = 1
                HAVING current_stock <= r.min_quantity
            ";

            $shortages = $this->db->connection()->select($sql, [$companyId]);
            if (empty($shortages)) return 0;

            $this->db->connection()->insert(
                "INSERT INTO purchase_requests (company_id, request_date, status, created_by, created_at) VALUES (?, ?, ?, ?, ?)",
                [$companyId, date('Y-m-d'), 'draft', $userId, date('Y-m-d H:i:s')]
            );
            $prId = (int) $this->db->connection()->getPdo()->lastInsertId();

            foreach ($shortages as $shortage) {
                $qtyToOrder = (float)$shortage['max_quantity'] - (float)$shortage['current_stock'];
                
                if ($qtyToOrder > 0) {
                    $this->db->connection()->insert(
                        "INSERT INTO purchase_request_items (purchase_request_id, product_id, quantity, required_by_date) VALUES (?, ?, ?, ?)",
                        [$prId, $shortage['product_id'], $qtyToOrder, date('Y-m-d', strtotime('+14 days'))]
                    );
                }
            }

            return count($shortages);
        });
    }
}