<?php
// Path: app/Modules/Manufacturing/BOM/Infrastructure/BOMRepository.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Manufacturing\BOM\Domain\BOMRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: BOM
 */
class BOMRepository extends BaseRepository implements BOMRepositoryInterface
{
    protected string $table = 'manufacturing_boms';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getActiveBOMForProduct(int $productId, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('product_id', '=', $productId)
                       ->where('company_id', '=', $companyId)
                       ->where('is_active', '=', 1)
                       ->first();

        return $result ?: null;
    }

    /**
     * @inheritDoc
     */
    public function saveWithItems(array $bomData, array $items): int
    {
        $bomId = $this->create($bomData);

        if (empty($items)) {
            return $bomId;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $bomId,
                $item['component_product_id'],
                $item['quantity'],
                $item['unit_id'] ?? null,
                $item['scrap_percentage'] ?? 0.00
            );
        }

        $sql = "INSERT INTO manufacturing_bom_items 
                (bom_id, component_product_id, quantity, unit_id, scrap_percentage) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);

        return $bomId;
    }
}