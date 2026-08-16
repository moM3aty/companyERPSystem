<?php
// Path: app/Core/Settings/SettingsManager.php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Database\DatabaseManager;
use App\Core\Cache\CacheManager;
use App\Core\Helpers\Arr;

/**
 * Enterprise Settings Manager
 * المحرك المركزي لإدارة كافة إعدادات النظام.
 * يعتمد على استراتيجية (Cache-First) لضمان أداء صاروخي وعدم إرهاق قواعد البيانات.
 */
class SettingsManager
{
    protected DatabaseManager $db;
    protected CacheManager $cache;
    protected string $table = 'settings';

    /**
     * SettingsManager constructor.
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
     * جلب قيمة إعداد معين.
     *
     * @param string $scope نطاق الإعداد (global, company, branch, user)
     * @param int|null $scopeId المعرف الخاص بالنطاق (مثلاً رقم الشركة)
     * @param string $key مفتاح الإعداد (مثال: 'invoice.tax_rate')
     * @param mixed $default القيمة الافتراضية إذا لم يوجد
     * @return mixed
     */
    public function get(string $scope, ?int $scopeId, string $key, mixed $default = null): mixed
    {
        $settings = $this->loadScope($scope, $scopeId);

        // نستخدم Arr::get لتمكين الوصول المتعمق (Dot Notation) إذا كانت القيمة مصفوفة
        return Arr::get($settings, $key, $default);
    }

    /**
     * تحميل جميع الإعدادات الخاصة بنطاق معين بالكامل من الـ Cache أو الـ DB.
     *
     * @param string $scope
     * @param int|null $scopeId
     * @return array
     */
    public function loadScope(string $scope, ?int $scopeId): array
    {
        $cacheKey = $this->getCacheKey($scope, $scopeId);

        // تخزين في الكاش لمدة 24 ساعة، يتم تفريغه آلياً عند التعديل
        return $this->cache->remember($cacheKey, 86400, function () use ($scope, $scopeId) {
            
            $query = "SELECT `key`, `value`, `type` FROM {$this->table} WHERE scope = ?";
            $bindings = [$scope];

            if ($scopeId !== null) {
                $query .= " AND scope_id = ?";
                $bindings[] = $scopeId;
            } else {
                $query .= " AND scope_id IS NULL";
            }

            $rows = $this->db->connection()->select($query, $bindings);
            
            $settings = [];
            foreach ($rows as $row) {
                $setting = new Setting($row);
                // استخدام الـ Dot Notation لبناء مصفوفة متداخلة (Nested Array) بشكل ذكي
                Arr::set($settings, $row['key'], $setting->getTypedValue());
            }

            return $settings;
        });
    }


    /**
     * حفظ أو تحديث إعداد معين.
     *
     * @param string $scope
     * @param int|null $scopeId
     * @param string $key
     * @param mixed $value
     * @param string $type نوع البيانات للـ Casting
     * @return void
     */
    public function set(string $scope, ?int $scopeId, string $key, mixed $value, string $type = 'string'): void
    {
        // تجهيز القيمة للحفظ
        $dbValue = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;

        // التحقق مما إذا كان الإعداد موجود مسبقاً
        $checkQuery = "SELECT id FROM {$this->table} WHERE scope = ? AND `key` = ?";
        $bindings = [$scope, $key];

        if ($scopeId !== null) {
            $checkQuery .= " AND scope_id = ?";
            $bindings[] = $scopeId;
        } else {
            $checkQuery .= " AND scope_id IS NULL";
        }

        $existing = $this->db->connection()->selectOne($checkQuery, $bindings);

        if ($existing) {
            // تحديث الإعداد
            $updateQuery = "UPDATE {$this->table} SET `value` = ?, `type` = ?, updated_at = ? WHERE id = ?";
            $this->db->connection()->update($updateQuery, [$dbValue, $type, date('Y-m-d H:i:s'), $existing['id']]);
        } else {
            // إنشاء إعداد جديد
            $insertQuery = "INSERT INTO {$this->table} (scope, scope_id, `key`, `value`, `type`, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->db->connection()->insert($insertQuery, [
                $scope,
                $scopeId,
                $key,
                $dbValue,
                $type,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ]);
        }

        // مسح الـ Cache الخاص بهذا النطاق ليتم إعادة تحميله محدثاً في الطلب القادم
        $this->clearCache($scope, $scopeId);
    }


    /**
     * حذف إعداد معين من قاعدة البيانات.
     *
     * @param string $scope
     * @param int|null $scopeId
     * @param string $key
     * @return void
     */
    public function forget(string $scope, ?int $scopeId, string $key): void
    {
        $deleteQuery = "DELETE FROM {$this->table} WHERE scope = ? AND `key` = ?";
        $bindings = [$scope, $key];

        if ($scopeId !== null) {
            $deleteQuery .= " AND scope_id = ?";
            $bindings[] = $scopeId;
        } else {
            $deleteQuery .= " AND scope_id IS NULL";
        }

        $this->db->connection()->delete($deleteQuery, $bindings);

        $this->clearCache($scope, $scopeId);
    }

    /**
     * مسح الـ Cache لنطاق معين.
     *
     * @param string $scope
     * @param int|null $scopeId
     * @return void
     */
    public function clearCache(string $scope, ?int $scopeId): void
    {
        $this->cache->delete($this->getCacheKey($scope, $scopeId));
    }

    /**
     * توليد مفتاح الـ Cache بشكل منظم.
     *
     * @param string $scope
     * @param int|null $scopeId
     * @return string
     */
    protected function getCacheKey(string $scope, ?int $scopeId): string
    {
        return "settings_{$scope}_" . ($scopeId ?? 'global');
    }
}