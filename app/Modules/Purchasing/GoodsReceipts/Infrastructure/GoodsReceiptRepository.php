<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Infrastructure/GoodsReceiptRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\GoodsReceipts\Domain\GoodsReceiptRepositoryInterface;

class GoodsReceiptRepository extends BaseRepository implements GoodsReceiptRepositoryInterface
{
    protected string $table = 'purchasing_goods_receipts';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateReceiptNumber(int $companyId): string
    {
        $prefix = 'GRN-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['receipt_no'])
            ->where('company_id', '=', $companyId)
            ->where('receipt_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['receipt_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $receiptId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $receiptId,
                $item['product_id'],
                $item['warehouse_id'],
                $item['ordered_quantity'] ?? 0.0,
                $item['received_quantity'],
                $item['unit_cost'] ?? 0.0
            );
        }

        $sql = "INSERT INTO purchasing_goods_receipt_items 
                (goods_receipt_id, product_id, warehouse_id, ordered_quantity, received_quantity, unit_cost) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}