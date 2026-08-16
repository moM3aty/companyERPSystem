<?php
// File 3: app/Modules/SupplyChain/Services/LandedCostService.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class LandedCostService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    /**
     * توزيع التكلفة الإضافية على أصناف الاستلام بناءً على طريقة التوزيع.
     */
    public function allocateCosts(int $landedCostId, int $companyId): void
    {
        $this->transaction->execute(function () use ($landedCostId, $companyId) {
            $landedCost = $this->db->connection()->selectOne(
                "SELECT * FROM landed_costs WHERE id = ? AND company_id = ? AND status = 'draft' FOR UPDATE",
                [$landedCostId, $companyId]
            );

            if (!$landedCost) {
                throw new BusinessException("Landed cost document not found or already processed.");
            }

            $items = $this->db->connection()->select(
                "SELECT id, product_id, quantity, unit_price, (quantity * unit_price) as line_total 
                 FROM goods_receipt_items WHERE goods_receipt_id = ?",
                [$landedCost['goods_receipt_id']]
            );

            $totalBaseValue = array_sum(array_column($items, 'line_total'));
            $totalQty = array_sum(array_column($items, 'quantity'));

            if ($totalBaseValue <= 0 || $totalQty <= 0) {
                throw new BusinessException("Cannot allocate costs to a receipt with zero value or quantity.");
            }

            $totalAdditionalCost = (float) $landedCost['total_additional_cost'];
            $method = $landedCost['allocation_method'];

            foreach ($items as $item) {
                $allocatedAmount = 0.0;
                
                // خوارزمية التوزيع القياسية في הـ ERP
                if ($method === 'value') {
                    $ratio = $item['line_total'] / $totalBaseValue;
                    $allocatedAmount = $totalAdditionalCost * $ratio;
                } elseif ($method === 'quantity') {
                    $ratio = $item['quantity'] / $totalQty;
                    $allocatedAmount = $totalAdditionalCost * $ratio;
                }

                $newUnitCost = $item['unit_price'] + ($allocatedAmount / $item['quantity']);

                // تحديث تكلفة الصنف في إيصال الاستلام ليؤثر على تكلفة المخزون (Moving Average)
                $this->db->connection()->update(
                    "UPDATE goods_receipt_items SET landed_cost_amount = ?, final_unit_cost = ? WHERE id = ?",
                    [$allocatedAmount, $newUnitCost, $item['id']]
                );
            }

            $this->db->connection()->update(
                "UPDATE landed_costs SET status = 'allocated', updated_at = ? WHERE id = ?",
                [date('Y-m-d H:i:s'), $landedCostId]
            );
        });
    }
}