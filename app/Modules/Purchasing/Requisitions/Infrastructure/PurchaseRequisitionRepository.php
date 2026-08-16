<?php
// Path: app/Modules/Purchasing/Requisitions/Infrastructure/PurchaseRequisitionRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\Requisitions\Domain\PurchaseRequisitionRepositoryInterface;

class PurchaseRequisitionRepository extends BaseRepository implements PurchaseRequisitionRepositoryInterface
{
    protected string $table = 'purchase_requisitions';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generatePrNumber(int $companyId): string
    {
        $prefix = 'PR-' . date('ym') . '-';
        
        $lastPr = $this->newQuery()
            ->select(['pr_number'])
            ->where('company_id', '=', $companyId)
            ->where('pr_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPr) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastPr['pr_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $prId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $prId,
                $item['product_id'],
                $item['description'] ?? null,
                $item['quantity'],
                $item['estimated_unit_price'] ?? 0.00,
                $item['total_estimated'] ?? 0.00
            );
        }

        $sql = "INSERT INTO purchase_requisition_items 
                (purchase_requisition_id, product_id, description, quantity, estimated_unit_price, total_estimated) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}