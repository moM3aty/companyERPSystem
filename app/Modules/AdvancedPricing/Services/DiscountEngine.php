<?php
// Path: app/Modules/AdvancedPricing/Services/DiscountEngine.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Discount Engine
 * محرك الخصومات الذكي: يبحث عن أفضل قاعدة خصم (Discount Rule) تنطبق على سلة المشتريات
 * بناءً على الكمية المشتراة أو إجمالي المبلغ.
 */
class DiscountEngine
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * احتساب أفضل نسبة خصم متوفرة بناءً على الكمية والقيمة.
     */
    public function getBestDiscount(float $quantity, float $totalAmount, int $companyId): float
    {
        // جلب جميع قواعد الخصم الفعالة التي تنطبق على هذه الكمية أو هذا المبلغ
        $sql = "
            SELECT discount_percentage 
            FROM advanced_pricing_discount_rules 
            WHERE company_id = ? 
              AND is_active = 1 
              AND valid_from <= CURRENT_DATE 
              AND valid_to >= CURRENT_DATE
              AND min_quantity <= ? 
              AND min_amount <= ?
            ORDER BY discount_percentage DESC
            LIMIT 1
        ";

        $result = $this->db->connection()->selectOne($sql, [$companyId, $quantity, $totalAmount]);

        return $result ? (float)$result['discount_percentage'] : 0.0;
    }
}