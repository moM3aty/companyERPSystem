<?php
// Path: app/Core/Localization/TranslationManager.php

declare(strict_types=1);

namespace App\Core\Localization;

use App\Core\Cache\CacheManager;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Translation Manager
 * يدير استدعاء النصوص المترجمة من قاعدة البيانات ويوفر نظام Caching فائق السرعة
 * لمنع عمل Query لكل كلمة في الواجهة.
 */
class TranslationManager
{
    protected DatabaseManager $db;
    protected CacheManager $cache;
    protected string $fallbackLocale = 'en';

    /**
     * TranslationManager constructor.
     *
     * @param DatabaseManager $db
     * @param CacheManager $cache
     */
    public function __construct(DatabaseManager $db, CacheManager $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * جلب نص مترجم بناءً على اللغة والمفتاح.
     * يدعم نظام (Dot Notation) مثل: 'module.key'
     *
     * @param string $locale
     * @param string $key
     * @return string|null
     */
    public function getLine(string $locale, string $key): ?string
    {
        // 1. محاولة الجلب للغة المطلوبة
        $translations = $this->loadTranslations($locale);
        
        if (isset($translations[$key])) {
            return $translations[$key];
        }

        // 2. إذا لم نجدها، نحاول مع اللغة الافتراضية (Fallback)
        if ($locale !== $this->fallbackLocale) {
            $fallbackTranslations = $this->loadTranslations($this->fallbackLocale);
            if (isset($fallbackTranslations[$key])) {
                return $fallbackTranslations[$key];
            }
        }

        return null; // لم نجد النص في أي مكان
    }

    /**
     * تحميل جميع نصوص لغة معينة في الذاكرة باستخدام الـ Cache.
     *
     * @param string $locale
     * @return array
     */
    public function loadTranslations(string $locale): array
    {
        $cacheKey = "translations_{$locale}";

        return $this->cache->remember($cacheKey, 86400, function () use ($locale) {
            $rows = $this->db->connection()->select(
                "SELECT translation_key, translation_value FROM translations WHERE locale = ?",
                [$locale]
            );

            $dictionary = [];
            foreach ($rows as $row) {
                $dictionary[$row['translation_key']] = $row['translation_value'];
            }

            return $dictionary;
        });
    }

    /**
     * تفريغ كاش الترجمة عند إضافة أو تعديل نصوص من لوحة التحكم.
     *
     * @param string $locale
     * @return void
     */
    public function clearCache(string $locale): void
    {
        $this->cache->delete("translations_{$locale}");
    }
}