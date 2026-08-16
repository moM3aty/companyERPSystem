<?php
// Path: app/Core/Localization/NumberFormatter.php

declare(strict_types=1);

namespace App\Core\Localization;

/**
 * Enterprise Number Formatter
 * يعالج أرقام الفواتير والمبالغ ليتم عرضها حسب المنطقة الجغرافية (Locale).
 * يفرق بين استخدام النقطة (1,000.50) والفاصلة (1.000,50) بناءً على إعدادات الـ User/Company.
 */
class NumberFormatter
{
    protected string $localeCode;

    /**
     * NumberFormatter constructor.
     *
     * @param string $localeCode (مثال: 'en-US', 'de-DE', 'ar-SA')
     */
    public function __construct(string $localeCode = 'en-US')
    {
        $this->localeCode = $localeCode;
    }

    /**
     * فرمتة الرقم بناءً على المعيار الإقليمي.
     *
     * @param float $number
     * @param int $decimals عدد الخانات العشرية
     * @return string
     */
    public function format(float $number, int $decimals = 2): string
    {
        // إذا كانت إضافة الـ intl مفعلة في السيرفر (وهو المعيار الأفضل في الـ Enterprise)
        if (class_exists('\NumberFormatter')) {
            $formatter = new \NumberFormatter($this->localeCode, \NumberFormatter::DECIMAL);
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $decimals);
            
            $result = $formatter->format($number);
            return $result !== false ? $result : (string) $number;
        }

        // Fallback في حالة عدم توفر المكتبة
        return $this->manualFormat($number, $decimals);
    }

    /**
     * فرمتة يدوية تعتمد على خصائص اللغات المعروفة.
     *
     * @param float $number
     * @param int $decimals
     * @return string
     */
    protected function manualFormat(float $number, int $decimals): string
    {
        // اللغات الأوروبية تستخدم الفاصلة للعشري والنقطة للآلاف
        $europeanLocales = ['de', 'fr', 'es', 'it'];
        $baseLocale = explode('-', $this->localeCode)[0];

        if (in_array($baseLocale, $europeanLocales, true)) {
            return number_format($number, $decimals, ',', '.');
        }

        // الصيغة القياسية الأمريكية/العربية
        return number_format($number, $decimals, '.', ',');
    }
}