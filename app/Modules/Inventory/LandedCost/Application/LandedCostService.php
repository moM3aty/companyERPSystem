<?php
// Path: app/Modules/Inventory/LandedCost/Application/LandedCostService.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Application;

use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Inventory\LandedCost\Domain\LandedCostRepositoryInterface;

/**
 * Enterprise Application Service: Landed Cost Engine
 * المحرك المالي الأعقد في المخازن. يقوم بتوزيع التكاليف الإضافية وإعادة حساب (متوسط التكلفة) 
 * لكل صنف تم استلامه بأثر رجعي، ليعكس القيمة الحقيقية للمخزون.
 */
class LandedCostService
{
    protected LandedCostRepositoryInterface $repo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;

    public function __construct(
        LandedCostRepositoryInterface $repo,
        TransactionManager $transaction,
        DatabaseManager $db
    ) {
        $this->repo = $repo;
        $this->transaction = $transaction;
        $this->db = $db;
    }

    public function applyCost(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            $receiptId = (int) $data['goods_receipt_id'];
            $totalCost = (float) $data['total_cost'];
            $method = $data['allocation_method'];

            // 1. جلب سطور إذن الاستلام (GRN Items)
            $grnItems = $this->db->connection()->select(
                "SELECT id, product_id, received_quantity, unit_cost 
                 FROM purchasing_goods_receipt_items 
                 WHERE goods_receipt_id = ?",
                [$receiptId]
            );

            if (empty($grnItems)) {
                throw new BusinessException("Cannot apply landed cost. The selected Goods Receipt is empty.");
            }

            // 2. حساب المعامل المرجعي (الإجمالي الكلي للكمية أو القيمة) لتوزيع التكلفة
            $totalReferenceValue = 0.0;
            foreach ($grnItems as $item) {
                $qty = (float) $item['received_quantity'];
                $cost = (float) $item['unit_cost'];
                $totalReferenceValue += ($method === 'by_value') ? ($qty * $cost) : $qty;
            }

            if ($totalReferenceValue <= 0.0) {
                throw new BusinessException("Cannot allocate costs. The reference total (value/qty) is zero.");
            }

            // 3. إنشاء ترويسة الـ Landed Cost
            $landedCostId = $this->repo->create([
                'company_id'          => $companyId,
                'goods_receipt_id'    => $receiptId,
                'purchase_invoice_id' => $data['purchase_invoice_id'],
                'total_cost'          => $totalCost,
                'allocation_method'   => $method,
                'status'              => 'posted', // يُرحل فوراً للتأثير في التكلفة
                'created_by'          => $userId,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);

            $allocations = [];

            // 4. توزيع التكلفة وتحديث متوسط التكلفة لكل صنف في المخزون (Moving Average Cost)
            foreach ($grnItems as $item) {
                $qty = (float) $item['received_quantity'];
                $cost = (float) $item['unit_cost'];
                
                $itemRefValue = ($method === 'by_value') ? ($qty * $cost) : $qty;
                
                // حساب نصيب هذا السطر من التكلفة الإضافية
                $allocatedAmount = round(($itemRefValue / $totalReferenceValue) * $totalCost, 2);

                $allocations[] = [
                    'goods_receipt_item_id' => $item['id'],
                    'product_id'            => $item['product_id'],
                    'allocated_amount'      => $allocatedAmount,
                ];

                // إعادة حساب متوسط التكلفة في المخزون الرئيسي للصنف
                if ($allocatedAmount > 0 && $qty > 0) {
                    $extraCostPerUnit = $allocatedAmount / $qty;
                    
                    // استدعاء رصيد الصنف وتحديث تكلفته (Pessimistic Lock لمنع التضارب)
                    // في النظام الكامل يتم استدعاء הـ StockRepository->lockForUpdate هنا
                    $this->db->connection()->statement(
                        "UPDATE inventory_stocks 
                         SET average_cost = average_cost + ? 
                         WHERE product_id = ? AND company_id = ?",
                        [$extraCostPerUnit, $item['product_id'], $companyId]
                    );
                }
            }

            // 5. حفظ التوزيعات (Allocations)
            $this->repo->bulkInsertAllocations($landedCostId, $allocations);

            return $landedCostId;
        });
    }
}