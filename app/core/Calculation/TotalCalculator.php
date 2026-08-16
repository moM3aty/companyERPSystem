<?php
// Path: app/Core/Calculation/TotalCalculator.php

declare(strict_types=1);

namespace App\Core\Calculation;

/**
 * Enterprise Total Calculator
 * محرك مخصص لضمان دقة احتساب إجماليات السطور في الفواتير (Line Items).
 * يطبق القاعدة المحاسبية القياسية: 
 * (الكمية × السعر) = الإجمالي الفرعي -> نطرح الخصم -> نحسب الضريبة على المتبقي -> نجمع الضريبة.
 */
class TotalCalculator
{
    /**
     * حساب إجمالي السطر الواحد بكامل تفاصيله.
     *
     * @param float $quantity الكمية
     * @param float $unitPrice سعر الوحدة
     * @param float $discountAmount مبلغ الخصم على إجمالي السطر
     * @param Percentage $taxRate نسبة الضريبة
     * @param bool $isTaxInclusive هل سعر الوحدة شامل الضريبة؟
     * @return array مصفوفة تحتوي على كل التفاصيل الموزونة بدقة
     */
    public static function calculateLineItem(
        float $quantity, 
        float $unitPrice, 
        float $discountAmount = 0.0, 
        Percentage $taxRate = new Percentage(0.0),
        bool $isTaxInclusive = false
    ): array {
        
        $grossTotal = RoundingService::roundFinancial($quantity * $unitPrice);
        $netBeforeTax = DiscountCalculator::applyFixedDiscount($grossTotal, $discountAmount);
        
        $taxAmount = 0.0;
        $netTotal = $netBeforeTax; // النهائي

        if ($isTaxInclusive) {
            // الضريبة مستقطعة من الداخل
            $taxAmount = TaxCalculator::extractTaxFromInclusive($netBeforeTax, $taxRate);
            // الإجمالي النهائي لا يتغير، لكن المبلغ قبل الضريبة يقل
            $netBeforeTax = RoundingService::roundFinancial($netTotal - $taxAmount);
        } else {
            // الضريبة تُضاف من الخارج
            $taxAmount = TaxCalculator::calculateExclusiveTax($netBeforeTax, $taxRate);
            $netTotal = RoundingService::roundFinancial($netBeforeTax + $taxAmount);
        }

        return [
            'quantity'       => $quantity,
            'unit_price'     => $unitPrice,
            'gross_total'    => $grossTotal,
            'discount'       => $discountAmount,
            'net_before_tax' => $netBeforeTax,
            'tax_amount'     => $taxAmount,
            'net_total'      => $netTotal, // المبلغ الذي سيدفعه العميل فعلياً عن هذا السطر
        ];
    }
}