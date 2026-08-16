<?php
// Path: app/Modules/Sales/Promotions/Infrastructure/PromotionRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\Promotions\Domain\PromotionRepositoryInterface;

class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected string $table = 'sales_promotions';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getActivePromotionsForProduct(int $productId, int $companyId): array
    {
        $now = date('Y-m-d H:i:s');
        
        // جلب العروض السارية على هذا المنتج تحديداً، أو العروض السارية على كافة المنتجات (product_id IS NULL)
        $sql = "SELECT * FROM {$this->table} 
                WHERE company_id = ? 
                  AND (product_id = ? OR product_id IS NULL)
                  AND is_active = 1
                  AND (start_date IS NULL OR start_date <= ?)
                  AND (end_date IS NULL OR end_date >= ?)
                ORDER BY discount_percent DESC, fixed_price ASC";

        return $this->db->connection()->select($sql, [$companyId, $productId, $now, $now]);
    }
}