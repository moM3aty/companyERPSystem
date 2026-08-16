<?php
// Path: app/Core/FeatureFlags/FeatureManager.php

declare(strict_types=1);

namespace App\Core\FeatureFlags;

use App\Core\Cache\CacheManager;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Feature Manager
 * المحرك الذي يقيم ما إذا كانت ميزة معينة متاحة أم لا (Evaluation Engine).
 * يعتمد بشكل كثيف على الـ Cache لتوفير أداء فائق.
 */
class FeatureManager
{
    protected DatabaseManager $db;
    protected CacheManager $cache;

    /**
     * FeatureManager constructor.
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
     * فحص ما إذا كانت الميزة مفعلة.
     *
     * @param string $featureCode كود الميزة (مثال: 'beta_tax_engine')
     * @param FeatureContext|null $context
     * @return bool
     */
    public function isEnabled(string $featureCode, ?FeatureContext $context = null): bool
    {
        $feature = $this->getFeature($featureCode);

        // الميزة غير موجودة أو معطلة بالكامل
        if (!$feature || $feature->getAttribute('is_active') === false) {
            return false;
        }

        // الميزة مفعلة عالمياً لكل الشركات والمستخدمين
        if ($feature->isGloballyEnabled()) {
            return true;
        }

        // الميزة غير مفعلة عالمياً، ولا يوجد سياق لفحصه (Guest)
        if (!$context) {
            return false;
        }

        // التحقق من القواعد المخصصة (Targeting Rules)
        return $this->evaluateRules((int) $feature->getAttribute('id'), $context);
    }

    /**
     * جلب الميزة من الـ Cache أو الداتا بيز.
     *
     * @param string $code
     * @return Feature|null
     */
    protected function getFeature(string $code): ?Feature
    {
        $cacheKey = "feature_toggle_{$code}";

        $data = $this->cache->remember($cacheKey, 3600, function () use ($code) {
            return $this->db->connection()->selectOne(
                "SELECT * FROM features WHERE code = ? LIMIT 1",
                [$code]
            );
        });

        return $data ? new Feature($data) : null;
    }

    /**
     * تقييم القواعد المخصصة للميزة لمعرفة ما إذا كانت متاحة لهذا المستخدم أو الشركة.
     *
     * @param int $featureId
     * @param FeatureContext $context
     * @return bool
     */
    protected function evaluateRules(int $featureId, FeatureContext $context): bool
    {
        $cacheKey = "feature_rules_{$featureId}";

        $rulesData = $this->cache->remember($cacheKey, 3600, function () use ($featureId) {
            return $this->db->connection()->select(
                "SELECT * FROM feature_rules WHERE feature_id = ?",
                [$featureId]
            );
        });

        foreach ($rulesData as $ruleArray) {
            $rule = new FeatureRule($ruleArray);
            $type = $rule->getAttribute('target_type');
            $targetId = (int) $rule->getAttribute('target_id');
            $isEnabled = (bool) $rule->getAttribute('is_enabled');

            // إذا تطابقت القاعدة مع سياق الشركة أو المستخدم وتم تعطيلها صراحة
            if (!$isEnabled) {
                if ($type === 'user' && $targetId === $context->userId) return false;
                if ($type === 'company' && $targetId === $context->companyId) return false;
            }

            // إذا تطابقت القاعدة ومفعلة صراحة
            if ($isEnabled) {
                if ($type === 'user' && $targetId === $context->userId) return true;
                if ($type === 'company' && $targetId === $context->companyId) return true;
                if ($type === 'branch' && $targetId === $context->branchId) return true;
            }
        }

        // الافتراضي هو الرفض إذا لم تتطابق أي قاعدة
        return false;
    }
}