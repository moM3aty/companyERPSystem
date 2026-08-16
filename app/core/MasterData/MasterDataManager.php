<?php
// Path: app/Core/MasterData/MasterDataManager.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Database\DatabaseManager;
use App\Core\Cache\CacheManager;

/**
 * Enterprise Master Data Manager (Facade)
 * المركز الرئيسي لجلب البيانات المرجعية. 
 * يعتمد على الـ CacheManager لتخزين النتائج ومنع إرهاق قاعدة البيانات بطلبات القوائم الثابتة.
 */
class MasterDataManager
{
    protected DatabaseManager $db;
    protected CacheManager $cache;

    /**
     * MasterDataManager constructor.
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
     * جلب جميع الدول المفعلة في النظام (Cached لمدة يوم كامل).
     *
     * @return array Array of Country objects
     */
    public function getActiveCountries(): array
    {
        return $this->cache->remember('master_data_countries', 86400, function () {
            $rows = $this->db->connection()->select("SELECT * FROM countries WHERE is_active = 1 ORDER BY name ASC");
            return array_map(fn($row) => new Country($row), $rows);
        });
    }

    /**
     * جلب جميع العملات المفعلة في النظام (Cached لمدة ساعة).
     *
     * @return array Array of Currency objects
     */
    public function getActiveCurrencies(): array
    {
        return $this->cache->remember('master_data_currencies', 3600, function () {
            $rows = $this->db->connection()->select("SELECT * FROM currencies WHERE is_active = 1 ORDER BY code ASC");
            return array_map(fn($row) => new Currency($row), $rows);
        });
    }

    /**
     * جلب القوائم المرجعية لشركة معينة بناءً على النوع (مثال: customer_categories).
     *
     * @param string $type
     * @param int $companyId
     * @return array Array of Lookup objects
     */
    public function getLookupsByType(string $type, int $companyId): array
    {
        $cacheKey = "master_data_lookup_{$type}_company_{$companyId}";

        return $this->cache->remember($cacheKey, 3600, function () use ($type, $companyId) {
            $rows = $this->db->connection()->select(
                "SELECT * FROM lookups WHERE type = ? AND company_id = ? AND is_active = 1 ORDER BY sort_order ASC",
                [$type, $companyId]
            );
            return array_map(fn($row) => new Lookup($row), $rows);
        });
    }

    /**
     * جلب سعر الصرف بين عملتين في تاريخ محدد (لا يُخزن في الـ Cache لضرورة دقته اللحظية).
     *
     * @param int $companyId
     * @param int $baseCurrencyId
     * @param int $targetCurrencyId
     * @param string $date (YYYY-MM-DD)
     * @return float|null
     */
    public function getExchangeRate(int $companyId, int $baseCurrencyId, int $targetCurrencyId, string $date): ?float
    {
        if ($baseCurrencyId === $targetCurrencyId) {
            return 1.000000;
        }

        $query = "SELECT rate FROM exchange_rates 
                  WHERE company_id = ? 
                  AND base_currency_id = ? 
                  AND target_currency_id = ? 
                  AND effective_date <= ? 
                  ORDER BY effective_date DESC LIMIT 1";

        $row = $this->db->connection()->selectOne($query, [$companyId, $baseCurrencyId, $targetCurrencyId, $date]);

        return $row ? (float) $row['rate'] : null;
    }
}