<?php
// Path: app/Modules/AdvancedPricing/Services/ContractPricingService.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Contract Pricing Service
 * المسؤول عن فحص ما إذا كان للعميل عقد مسبق بسعر خاص للمنتج.
 */
class ContractPricingService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function getCustomerContractPrice(int $customerId, int $productId, int $companyId): float
    {
        if ($customerId <= 0) return 0.0;

        $sql = "
            SELECT agreed_price 
            FROM advanced_pricing_customer_contracts 
            WHERE customer_id = ? AND product_id = ? AND company_id = ? 
              AND valid_from <= CURRENT_DATE AND valid_to >= CURRENT_DATE 
              AND is_active = 1 
            LIMIT 1
        ";

        $row = $this->db->connection()->selectOne($sql, [$customerId, $productId, $companyId]);
        
        return $row ? (float)$row['agreed_price'] : 0.0;
    }
}