<?php
// Path: app/Modules/Inventory/Transfers/Infrastructure/StockTransferRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Transfers\Domain\StockTransferRepositoryInterface;

class StockTransferRepository extends BaseRepository implements StockTransferRepositoryInterface
{
    protected string $table = 'inventory_stock_transfers';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateTransferNumber(int $companyId): string
    {
        $prefix = 'TRF-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['transfer_no'])
            ->where('company_id', '=', $companyId)
            ->where('transfer_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['transfer_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $transferId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $transferId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_cost'] ?? 0.00
            );
        }

        $sql = "INSERT INTO inventory_stock_transfer_items 
                (stock_transfer_id, product_id, quantity, unit_cost) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}