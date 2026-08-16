<?php
// Path: app/Core/Calculation/RoundingService.php

declare(strict_types=1);

namespace App\Core\Calculation;

/**
 * Enterprise Rounding Service
 * المحرك المركزي لتقريب الأرقام في النظام. 
 * توحيد طريقة التقريب في الـ ERP هو أمر حتمي لتجنب الفروقات المحاسبية (Rounding Discrepancies).
 */
class RoundingService
{
    /**
     * تقريب مالي قياسي (Round Half Up).
     * هذه هي الطريقة المحاسبية المعتمدة في الفواتير والضرائب.
     *
     * @param float $amount المبلغ المراد تقريبه
     * @param int $precision عدد الخانات العشرية (افتراضياً 2 للعملات الشائعة)
     * @return float
     */
    public static function roundFinancial(float $amount, int $precision = 2): float
    {
        return round($amount, $precision, PHP_ROUND_HALF_UP);
    }

    /**
     * التقريب البنكي (Round Half to Even / Banker's Rounding).
     * يستخدم في بعض الأنظمة المالية الدقيقة لتقليل الانحياز الإحصائي عند تقريب آلاف الأرقام.
     *
     * @param float $amount
     * @param int $precision
     * @return float
     */
    public static function roundBankers(float $amount, int $precision = 2): float
    {
        return round($amount, $precision, PHP_ROUND_HALF_EVEN);
    }

    /**
     * التقريب للأعلى دائماً (Ceil) بناءً على دقة معينة.
     *
     * @param float $amount
     * @param int $precision
     * @return float
     */
    public static function roundUp(float $amount, int $precision = 2): float
    {
        $multiplier = 10 ** $precision;
        return ceil($amount * $multiplier) / $multiplier;
    }

    /**
     * التقريب للأسفل دائماً (Floor) بناءً على دقة معينة.
     *
     * @param float $amount
     * @param int $precision
     * @return float
     */
    public static function roundDown(float $amount, int $precision = 2): float
    {
        $multiplier = 10 ** $precision;
        return floor($amount * $multiplier) / $multiplier;
    }
}