<?php
// Path: app/Core/Audit/EntityHistory.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Tenant\TenantContext;

/**
 * Enterprise Entity History Service
 * خدمة لتغليف وتنسيق تاريخ التعديلات لكيان معين، وتستخدم عادة لعرض الـ (Timeline) في واجهة المستخدم.
 */
class EntityHistory
{
    protected AuditRepository $repository;
    protected TenantContext $tenantContext;

    /**
     * EntityHistory constructor.
     *
     * @param AuditRepository $repository
     * @param TenantContext $tenantContext
     */
    public function __construct(AuditRepository $repository, TenantContext $tenantContext)
    {
        $this->repository = $repository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * جلب تاريخ الكيان منسقاً.
     *
     * @param string $entityType (مثال: 'customers')
     * @param int $entityId (مثال: 15)
     * @return array
     */
    public function getTimeline(string $entityType, int $entityId): array
    {
        $companyId = $this->tenantContext->requireTenant()->companyId;

        $rawHistory = $this->repository->getEntityHistory($entityType, $entityId, $companyId);
        $timeline = [];

        foreach ($rawHistory as $log) {
            $oldValues = $log['old_values'] ? json_decode($log['old_values'], true) : [];
            $newValues = $log['new_values'] ? json_decode($log['new_values'], true) : [];

            $timeline[] = [
                'log_id'     => (int) $log['id'],
                'action'     => $log['action'],
                'user_name'  => $log['user_name'] ?? 'System Automated',
                'ip_address' => $log['ip_address'],
                'date'       => $log['created_at'],
                'changes'    => $this->formatChanges($oldValues, $newValues)
            ];
        }

        return $timeline;
    }

    /**
     * تنسيق التغييرات بشكل مقروء (لواجهات الويب).
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    protected function formatChanges(array $old, array $new): array
    {
        $changes = [];
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($keys as $key) {
            $changes[] = [
                'field' => $key,
                'from'  => $old[$key] ?? 'N/A',
                'to'    => $new[$key] ?? 'N/A',
            ];
        }

        return $changes;
    }
}