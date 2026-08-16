<?php
// Path: app/Core/Cache/CacheKey.php

declare(strict_types=1);

namespace App\Core\Cache;

/**
 * Enterprise Cache Key Generator
 * يضمن توليد مفاتيح كاش موحدة وآمنة، تمنع تداخل البيانات بين الشركات (Tenants) المختلفة.
 */
class CacheKey
{
    /**
     * توليد مفتاح كاش مرتبط بالشركة الحالية.
     *
     * @param string $prefix
     * @param int $companyId
     * @param array $identifiers أي معرفات إضافية (مثل ID الفاتورة)
     * @return string
     */
    public static function tenant(string $prefix, int $companyId, array $identifiers = []): string
    {
        $key = "tenant_{$companyId}:{$prefix}";

        if (!empty($identifiers)) {
            $key .= ':' . implode(':', $identifiers);
        }

        return strtolower($key);
    }

    /**
     * توليد مفتاح كاش عام للنظام.
     *
     * @param string $prefix
     * @param array $identifiers
     * @return string
     */
    public static function global(string $prefix, array $identifiers = []): string
    {
        $key = "global:{$prefix}";

        if (!empty($identifiers)) {
            $key .= ':' . implode(':', $identifiers);
        }

        return strtolower($key);
    }

    /**
     * توليد مفتاح كاش مخصص لمستخدم بعينه.
     *
     * @param string $prefix
     * @param int $userId
     * @return string
     */
    public static function user(string $prefix, int $userId): string
    {
        return strtolower("user_{$userId}:{$prefix}");
    }
}