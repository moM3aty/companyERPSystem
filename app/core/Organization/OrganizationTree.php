<?php
// Path: app/Core/Organization/OrganizationTree.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Database\DatabaseManager;
use App\Core\Cache\CacheManager;

/**
 * Enterprise Organization Tree Builder
 * يقوم ببناء شجرة الهيكل التنظيمي بالكامل من قاعدة البيانات وربط الأقسام ببعضها
 * باستخدام (Cache-First) لأن الهيكل الإداري لا يتغير كثيراً.
 */
class OrganizationTree
{
    protected DatabaseManager $db;
    protected CacheManager $cache;

    public function __construct(DatabaseManager $db, CacheManager $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * بناء الشجرة التنظيمية للشركة بشكل متداخل (Nested Array).
     *
     * @param int $companyId
     * @return array
     */
    public function buildTree(int $companyId): array
    {
        $cacheKey = "org_tree_company_{$companyId}";

        return $this->cache->remember($cacheKey, 86400, function () use ($companyId) {
            $nodes = $this->db->connection()->select(
                "SELECT * FROM organization_nodes WHERE company_id = ? AND is_active = 1 ORDER BY level ASC, name ASC",
                [$companyId]
            );

            return $this->buildNestedArray($nodes);
        });
    }

    /**
     * تحويل القائمة المسطحة إلى شجرة متداخلة.
     *
     * @param array $flatNodes
     * @param int|null $parentId
     * @return array
     */
    protected function buildNestedArray(array $flatNodes, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($flatNodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = $this->buildNestedArray($flatNodes, (int) $node['id']);
                
                $nodeData = $node;
                $nodeData['children'] = $children;
                
                $branch[] = $nodeData;
            }
        }

        return $branch;
    }

    /**
     * مسح كاش الشجرة عند حدوث أي تعديل إداري.
     *
     * @param int $companyId
     * @return void
     */
    public function clearTreeCache(int $companyId): void
    {
        $this->cache->delete("org_tree_company_{$companyId}");
    }
}