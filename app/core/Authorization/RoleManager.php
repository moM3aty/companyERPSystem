<?php
// Path: app/Core/Authorization/RoleManager.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Database\DatabaseManager;
use App\Core\Database\QueryBuilder;
use App\Core\Tenant\TenantContext;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Role Manager
 * يدير عمليات إنشاء الأدوار وتعيين الصلاحيات للمستخدمين بأمان وداخل سياق الشركة (Tenant Context).
 */
class RoleManager
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    /**
     * RoleManager constructor.
     *
     * @param DatabaseManager $db
     * @param TenantContext $tenant
     */
    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
    }

    /**
     * إنشاء دور (Role) جديد تابع للشركة الحالية.
     *
     * @param string $name
     * @param string $description
     * @return int معرف الدور الجديد
     * @throws BusinessException
     */
    public function createRole(string $name, string $description = ''): int
    {
        // استخدام requireTenant يضمن عدم إمكانية تنفيذ الدالة إذا لم يكن السياق مضبوطاً
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $query = new QueryBuilder($this->db->connection());
        
        return $query->table('roles')->insert([
            'company_id' => $companyId,
            'name' => $name,
            'description' => $description,
            'is_system_role' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * ربط صلاحية (Permission) بدور معين (Role).
     * يتحقق أمنياً من أن الدور يتبع لشركة المستخدم الحالي.
     *
     * @param int $roleId
     * @param int $permissionId
     * @return void
     * @throws BusinessException
     */
    public function assignPermissionToRole(int $roleId, int $permissionId): void
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        
        // 1. أمان تام: التحقق من أن الدور المراد التعديل عليه يتبع لشركة المستخدم
        $role = (new QueryBuilder($this->db->connection()))
                    ->table('roles')
                    ->where('id', '=', $roleId)
                    ->where('company_id', '=', $companyId)
                    ->first();
                      
        if (!$role) {
            throw new BusinessException("Security Violation: Role not found or does not belong to your company.", 403);
        }
        
        // 2. التحقق من عدم وجود الربط مسبقاً لمنع التكرار (Idempotency)
        $exists = (new QueryBuilder($this->db->connection()))
                    ->table('role_permissions')
                    ->where('role_id', '=', $roleId)
                    ->where('permission_id', '=', $permissionId)
                    ->first();
                    
        if (!$exists) {
            (new QueryBuilder($this->db->connection()))
                ->table('role_permissions')
                ->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId
                ]);
        }
    }

    /**
     * تعيين دور (Role) لمستخدم (User).
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     * @throws BusinessException
     */
    public function assignRoleToUser(int $userId, int $roleId): void
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        // 1. التحقق من أن المستخدم والدور ينتميان لنفس الشركة
        $user = clone (new QueryBuilder($this->db->connection()))
                    ->table('users')
                    ->where('id', '=', $userId)
                    ->where('company_id', '=', $companyId)
                    ->first();

        $role = clone (new QueryBuilder($this->db->connection()))
                    ->table('roles')
                    ->where('id', '=', $roleId)
                    ->where('company_id', '=', $companyId)
                    ->first();

        if (!$user || !$role) {
            throw new BusinessException("Security Violation: User or Role does not belong to the active company.", 403);
        }

        // 2. التحقق من عدم وجود الربط مسبقاً
        $exists = clone (new QueryBuilder($this->db->connection()))
                    ->table('user_roles')
                    ->where('user_id', '=', $userId)
                    ->where('role_id', '=', $roleId)
                    ->first();

        if (!$exists) {
            (new QueryBuilder($this->db->connection()))
                ->table('user_roles')
                ->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]);
        }
    }
}