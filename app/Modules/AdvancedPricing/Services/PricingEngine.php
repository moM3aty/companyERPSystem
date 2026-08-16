<?php
// Path: app/Modules/AdvancedPricing/Services/PricingEngine.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Pricing Engine
 * المسؤول عن جلب السعر الأساسي للمنتج.
 */
class PricingEngine
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function getBasePrice(int $productId, int $companyId): float
    {
        $row = $this->db->connection()->selectOne(
            "SELECT default_price FROM products WHERE id = ? AND company_id = ?",
            [$productId, $companyId]
        );
        return $row ? (float)$row['default_price'] : 0.0;
    }
}