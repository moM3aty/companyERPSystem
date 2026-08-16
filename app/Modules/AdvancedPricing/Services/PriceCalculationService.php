<?php
// Path: app/Modules/AdvancedPricing/Services/PriceCalculationService.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Services;

/**
 * Enterprise Application Service: Price Calculation Orchestrator
 * المايسترو الذي يجمع بين (السعر الأساسي، عقود العملاء، وقواعد الخصم) لاستخراج السعر النهائي للعميل.
 */
class PriceCalculationService
{
    protected PricingEngine $pricingEngine;
    protected DiscountEngine $discountEngine;
    protected ContractPricingService $contractPricing;

    public function __construct(
        PricingEngine $pricingEngine,
        DiscountEngine $discountEngine,
        ContractPricingService $contractPricing
    ) {
        $this->pricingEngine = $pricingEngine;
        $this->discountEngine = $discountEngine;
        $this->contractPricing = $contractPricing;
    }

    public function calculateFinalCartPrice(array $items, int $customerId, int $companyId): array
    {
        $processedItems = [];
        $grandTotal = 0.0;
        $totalDiscountAmount = 0.0;

        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $qty = (float)$item['quantity'];

            // 1. السعر الأساسي (من قائمة الأسعار)
            $basePrice = $this->pricingEngine->getBasePrice($productId, $companyId);

            // 2. فحص وجود سعر تعاقدي خاص بالعميل (يتغلب على السعر الأساسي)
            $contractPrice = $this->contractPricing->getCustomerContractPrice($customerId, $productId, $companyId);
            $activePrice = $contractPrice > 0 ? $contractPrice : $basePrice;

            $lineSubtotal = $activePrice * $qty;

            // 3. تطبيق محرك الخصومات
            $discountPercent = $this->discountEngine->getBestDiscount($qty, $lineSubtotal, $companyId);
            $discountValue = $lineSubtotal * ($discountPercent / 100);
            
            $lineTotal = $lineSubtotal - $discountValue;

            $processedItems[] = [
                'product_id'       => $productId,
                'quantity'         => $qty,
                'unit_price'       => $activePrice,
                'discount_percent' => $discountPercent,
                'discount_amount'  => $discountValue,
                'line_total'       => $lineTotal,
            ];

            $grandTotal += $lineTotal;
            $totalDiscountAmount += $discountValue;
        }

        return [
            'customer_id'    => $customerId,
            'items'          => $processedItems,
            'subtotal'       => $grandTotal + $totalDiscountAmount,
            'total_discount' => $totalDiscountAmount,
            'grand_total'    => $grandTotal
        ];
    }
}