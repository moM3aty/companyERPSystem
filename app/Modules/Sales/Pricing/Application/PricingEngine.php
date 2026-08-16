<?php
// Path: app/Modules/Sales/Pricing/Application/PricingEngine.php

declare(strict_types=1);

namespace App\Modules\Sales\Pricing\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Calculation\RoundingService;

/**
 * Enterprise Pricing Engine
 * محرك ذكي يقرر السعر النهائي للصنف لعميل معين بناءً على:
 * 1. قوائم الأسعار المربوطة بالعميل.
 * 2. العروض الترويجية النشطة (Promotions).
 * 3. تسعير الكميات (Volume Pricing).
 */
class PricingEngine
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * حساب أفضل سعر متوفر للعميل لضمان أعلى تنافسية وأدق حساب.
     *
     * @param int $productId
     * @param int $customerId
     * @param float $quantity
     * @param int $companyId
     * @return float السعر النهائي الموصى به
     */
    public function determineBestPrice(int $productId, int $customerId, float $quantity, int $companyId): float
    {
        $now = date('Y-m-d H:i:s');

        // 1. Base Price
        $product = $this->db->connection()->selectOne("SELECT default_price FROM products WHERE id = ?", [$productId]);
        $basePrice = (float) ($product['default_price'] ?? 0.0);

        // 2. Customer Price List & Volume Pricing
        $customer = $this->db->connection()->selectOne("SELECT price_list_id FROM customers WHERE id = ?", [$customerId]);
        $priceListId = $customer['price_list_id'] ?? null;

        $bestPrice = $basePrice;

        if ($priceListId) {
            $sql = "SELECT pli.unit_price, pli.discount_percent 
                    FROM sales_price_list_items pli
                    JOIN sales_price_lists pl ON pli.price_list_id = pl.id
                    WHERE pl.id = ? AND pli.product_id = ? AND pli.min_quantity <= ?
                      AND pl.is_active = 1 
                      AND (pl.valid_from IS NULL OR pl.valid_from <= ?)
                      AND (pl.valid_to IS NULL OR pl.valid_to >= ?)
                    ORDER BY pli.min_quantity DESC LIMIT 1";

            $priceRule = $this->db->connection()->selectOne($sql, [$priceListId, $productId, $quantity, $now, $now]);

            if ($priceRule) {
                if ((float) $priceRule['unit_price'] > 0) {
                    $bestPrice = (float) $priceRule['unit_price'];
                } elseif ((float) $priceRule['discount_percent'] > 0) {
                    $discount = $basePrice * ((float) $priceRule['discount_percent'] / 100);
                    $bestPrice = $basePrice - $discount;
                }
            }
        }

        // 3. Global Promotions (Customer always gets the lowest price)
        $promoSql = "SELECT discount_percent, fixed_price FROM sales_promotions 
                     WHERE product_id = ? AND company_id = ? AND is_active = 1
                       AND start_date <= ? AND end_date >= ?
                     ORDER BY discount_percent DESC LIMIT 1";
                     
        $promo = $this->db->connection()->selectOne($promoSql, [$productId, $companyId, $now, $now]);

        if ($promo) {
            $promoPrice = $basePrice;
            if ((float) $promo['fixed_price'] > 0) {
                $promoPrice = (float) $promo['fixed_price'];
            } elseif ((float) $promo['discount_percent'] > 0) {
                $promoPrice = $basePrice - ($basePrice * ((float) $promo['discount_percent'] / 100));
            }

            if ($promoPrice < $bestPrice) {
                $bestPrice = $promoPrice;
            }
        }

        return RoundingService::roundFinancial($bestPrice);
    }
}