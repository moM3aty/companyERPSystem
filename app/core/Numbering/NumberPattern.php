<?php
// Path: app/Core/Numbering/NumberPattern.php

declare(strict_types=1);

namespace App\Core\Numbering;

/**
 * Enterprise Number Pattern
 * يعالج أنماط الترقيم المعقدة ويستبدل المتغيرات بالقيم الفعلية (مثل {YYYY}, {MM}, {SEQ:5}).
 */
class NumberPattern
{
    protected string $pattern;

    /**
     * NumberPattern constructor.
     *
     * @param string $pattern مثال: "INV-{YYYY}-{MM}-{SEQ:5}"
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * تطبيق النمط وتوليد الرقم النهائي.
     *
     * @param int $sequenceNumber الرقم التسلسلي القادم من قاعدة البيانات
     * @param NumberingContext $context سياق الترقيم (التاريخ، الشركة)
     * @return string
     */
    public function format(int $sequenceNumber, NumberingContext $context): string
    {
        $formatted = $this->pattern;

        // 1. استبدال متغيرات التاريخ
        $date = $context->documentDate ? strtotime($context->documentDate) : time();
        $formatted = str_replace('{YYYY}', date('Y', $date), $formatted);
        $formatted = str_replace('{YY}', date('y', $date), $formatted);
        $formatted = str_replace('{MM}', date('m', $date), $formatted);
        $formatted = str_replace('{DD}', date('d', $date), $formatted);

        // 2. استبدال متغيرات الشركة والفرع
        if ($context->companyId) {
            $formatted = str_replace('{COMP}', str_pad((string) $context->companyId, 2, '0', STR_PAD_LEFT), $formatted);
        }
        if ($context->branchId) {
            $formatted = str_replace('{BR}', str_pad((string) $context->branchId, 2, '0', STR_PAD_LEFT), $formatted);
        }

        // 3. استبدال الرقم التسلسلي مع مراعاة الحشو (Padding) مثل {SEQ:5} -> 00001
        $formatted = preg_replace_callback('/\{SEQ:(\d+)\}/', function ($matches) use ($sequenceNumber) {
            $length = (int) $matches[1];
            return str_pad((string) $sequenceNumber, $length, '0', STR_PAD_LEFT);
        }, $formatted);

        // التراجع للوضع الافتراضي إذا كان النمط {SEQ} بدون طول محدد
        $formatted = str_replace('{SEQ}', (string) $sequenceNumber, $formatted);

        return $formatted;
    }

    /**
     * الحصول على النمط الأصلي.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}