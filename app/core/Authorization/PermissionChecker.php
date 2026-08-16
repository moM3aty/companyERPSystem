<?php
// Path: app/Core/Authorization/PermissionChecker.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Permission Checker
 * يتحقق من صلاحيات المستخدم من قاعدة البيانات ويدير عملية الـ Caching في الذاكرة
 * لمنع إرهاق قواعد البيانات بالاستعلامات المتكررة خلال نفس الـ Request.
 */
class PermissionChecker
{
    protected DatabaseManager $db;
    
    /**
     * Cache لتخزين صلاحيات المستخدمين خلال الـ Lifecycle الحالية.
     * [userId => ['module.resource.action', ...]]
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * PermissionChecker constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق مما إذا كان المستخدم يمتلك صلاحية محددة داخل إطار شركته.
     *
     * @param int $userId
     * @param int $companyId
     * @param string $module
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function hasPermission(int $userId, int $companyId, string $module, string $resource, string $action): bool
    {
        $this->loadPermissions($userId, $companyId);

        $permissionKey = "{$module}.{$resource}.{$action}";
        
        // فحص صارم للصلاحية المطلوبة
        return in_array($permissionKey, $this->cache[$userId], true);
    }

    /**
     * جلب كافة الصلاحيات للمستخدم وتخزينها مؤقتاً (Memory Cache).
     * يستخدم استعلام JOIN محسن وفعال جداً لجمع الصلاحيات من عدة جداول.
     *
     * @param int $userId
     * @param int $companyId
     * @return void
     */
    protected function loadPermissions(int $userId, int $companyId): void
    {
        // إذا تم تحميل الصلاحيات مسبقاً لهذا المستخدم، نتجاوز الاستعلام (Performance Boost)
        if (isset($this->cache[$userId])) {
            return;
        }

        $sql = "SELECT p.module, p.resource, p.action 
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                INNER JOIN user_roles ur ON rp.role_id = ur.role_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE ur.user_id = ? AND r.company_id = ? AND r.deleted_at IS NULL";
        
        $permissionsList = $this->db->connection()->select($sql, [$userId, $companyId]);
        
        $this->cache[$userId] = [];
        
        // تحويل النتيجة إلى نصوص مبسطة لتسهيل المقارنة السريعة
        foreach ($permissionsList as $perm) {
            $this->cache[$userId][] = $perm['module'] . '.' . $perm['resource'] . '.' . $perm['action'];
        }
    }
}