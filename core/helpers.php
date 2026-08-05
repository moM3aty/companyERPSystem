<?php
// core/helpers.php

/**
 * دوال مساعدة عامة لاستخدامها في جميع أنحاء النظام
 */
class Helpers {
    
    /**
     * تنسيق النص للعرض الآمن
     */
    public static function e(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * تنسيق مصفوفة للعرض الآمن
     */
    public static function eArray(array $array): array {
        return array_map([self::class, 'e'], $array);
    }
    
    /**
     * تنسيق كائن للعرض الآمن
     */
    public static function eObject(?object $obj): object {
        if ($obj === null) return (object)['id' => 0, 'name' => 'غير معروف'];
        
        return (object) array_map(function($value) {
            return is_string($value) ? self::e($value) : $value;
        }, (array) $obj);
    }
    
    /**
     * تنسيق قيمة للعرض في الجداول (اتجاه اتجاه null)
     */
    public static function displayValue($value, string $fallback = '—'): string {
        if ($value === null || $value === '') {
            return $fallback;
        }
        return is_numeric($value) ? number_format((float) $value, 2) : self::e($value);
    }
    
    /**
     * تنسيق المبلغ مع العملة
     */
    public static function formatMoney(float $amount, ?string $currency = null): string {
        $currency = $currency ?? 'ر.س';
        $formatted = number_format($amount, 2);
        return $formatted . ' ' . $currency;
    }
    
    /**
     * تنسيق التاريخ
     */
    public static function formatDate(string $date, string $format = 'Y-m-d'): string {
        if (empty($date) || $date === '0000-00-00 00:00:00') {
            return '—';
        }
        return date($format, strtotime($date));
    }
    
    /**
     * تنسيق التاريخ مع الوقت
     */
    public static function formatDateTime(string $date): string {
        return self::formatDate($date, 'Y-m-d h:i A');
    }
    
    /**
     * تنسيق الفرق الزمني (منذ)
     */
    public static function timeAgo(string $date): string {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'الآن';
        }
        
        $intervals = [
            12 * 30 * 24 * 365 => ['سنة', 'سنوات'],
            30 * 24 * 60      => ['شهر', 'أشهر'],
            7 * 24 * 60          => ['أسبوع', 'أسابيع'],
            24 * 60               => ['يوم', 'أيام'],
            60                    => ['دقيقة', 'دقائق'],
        ];
        
        foreach ($intervals as $seconds => $labels) {
            $count = floor($diff / $seconds);
            if ($count >= 1) {
                $singular = $labels[0];
                $plural = $labels[1];
                return "منذ {$count} {$count == 1 ? $singular : $plural}";
            }
        }
        
        return self::formatDate($date);
    }
    
    /**
     * إنشاء رقم فاتورة جديد
     */
    public static function generateInvoiceNumber(): string {
        $prefix = 'INV';
        $date = date('Ymd');
        $time = date('His');
        $random = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$date}-{$time}-{$random}";
    }
    
    /**
     * إنشاء رمز SKU
     */
    public static function generateSku(string $prefix = 'PRD'): string {
        $date = date('ymd');
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return "{$prefix}-{$date}-{$random}";
    }
    
    /**
     * تحديد نوع العميل بالعربية
     */
    public static function getTypeLabel(string $type): string {
        // استخدم switch بدلاً من match لتوافق PHP 7
        switch ($type) {
            case 'individual': return 'فرد';
            case 'company': return 'شركة';
            default: return 'غير محدد';
        }
    }
    
    /**
     * لون حالة المخزون
     */
    public static function stockStatusColor(int $quantity): string {
        if ($quantity <= 0) return 'danger';
        if ($quantity <= 10) return 'warning';
        return 'success';
    }
    
    /**
     * نص حالة المخزون بالعربية
     */
    public static function stockStatusLabel(int $quantity): string {
        if ($quantity <= 0) return 'نفذ';
        if ($quantity <= 10) return 'منخفض';
        return 'متوفر';
    }
    
    /**
     * تحديد لون الحالة للفاتورة
     */
    public static function invoiceStatusColor(string $status): string {
        switch ($status) {
            case 'paid': return 'success';
            case 'partial': return 'warning';
            case 'cancelled': return 'danger';
            default: return 'info';
        }
    }
    
    /**
     * نص حالة الفاتورة بالعربية
     */
    public static function invoiceStatusLabel(string $status): string {
        switch ($status) {
            case 'paid': return 'مدفوعة';
            case 'partial': return 'مدفوعة جزئياً';
            case 'cancelled': return 'ملغاة';
            default: return 'غير مدفوعة';
        }
    }
    
    /**
     * إنشاء شارة عشوائي للصورة
     */
    public static function avatarGradient(int $id): string {
        $gradients = [
            'linear-gradient(135deg, #14b8a6, #0d9488)',
            'linear-gradient(135deg, #f59e0b, #d97706)',
            'linear-gradient(135deg, #06b6d4, #0891b2)',
            'linear-gradient(135deg, #8b5cf6, #7c3aed)',
            'linear-gradient(135deg, #ec4899, #db2777)',
            'linear-gradient(135deg, #22c55e, #16a34a)',
        ];
        
        return $gradients[$id % count($gradients)];
    }
    
    /**
     * تقصير النص
     */
    public static function truncate(string $text, int $length = 50, string $end = '...'): string {
        if (mb_strlen($text) <= $length) {
            return self::e($text);
        }
        return self::e(mb_substr($text, 0, $length)) . $end;
    }
    
    /**
     * تحويل الأرقام إلى نص عربي
     */
    public static function numberToArabic(float $number): string {
        static $arabicDigits = [
            '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'
        ];
        
        $str = (string) $number;
        $result = '';
        
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            if (is_numeric($char)) {
                $result .= $arabicDigits[(int)$char];
            } else {
                $result .= $char;
            }
        }
        
        return $result;
    }
    
    /**
     * التحقق من صحة البريد الإلكتروني
     */
    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * التحقق من صيغة رقم الهاتف السعودي
     */
    public static function isValidPhone(string $phone): bool {
        return preg_match('/^05[0-9]{8}$/', $phone) === 1;
    }
    
    /**
     * تنظيف رقم الهاتف للعرض
     */
    public static function formatPhone(?string $phone): string {
        if (empty($phone)) return '—';
        
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return self::e(substr($phone, 0, 4) . ' ' . substr($phone, 4));
        }
        
        return self::e($phone);
    }
    
    /**
     * حساب النسبة المئوية
     */
    public static function percentage(float $part, float $total, int $decimals = 1): string {
        if ($total <= 0) return '0%';
        return number_format(($part / $total) * 100, $decimals) . '%';
    }
    
    /**
     * تحديد لون القيمة (أحمر/أخضر)
     */
    public static function valueColor(float $value): string {
        if ($value > 0) return 'danger';
        if ($value < 0) return 'success';
        return 'muted';
    }
    
    /**
     * الحصول على الاسم الأول للحروف الافتراضي
     */
    public static function getInitials(string $name): string {
        $name = trim($name);
        if (empty($name)) return 'م';
        
        $words = preg_split('/\s+/', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= mb_substr($word, 0, 1);
            if (mb_strlen($initials) >= 2) break;
        }
        
        // إذا كان الاسم مكونًا من كلمة واحدة، خذ أول حرفين منها
        if (mb_strlen($initials) < 2) {
            $initials = mb_substr($name, 0, 2);
        }
        
        return $initials ?: 'م';
    }
}