<?php
// Path: app/Core/Authorization/Policy.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy Base Class
 * الفئة الأساسية للسياسات المعقدة (Policies) التي تحدد صلاحيات المستخدم على (سجل محدد) بدلاً من الجدول ككل.
 */
abstract class Policy
{
    /**
     * يتم تشغيل هذه الدالة قبل أي فحص. 
     * إذا أرجعت true، يتجاوز النظام باقي الفحوصات (مفيدة جداً لإعطاء Super Admin صلاحية مطلقة).
     *
     * @param AuthUser $user
     * @return bool|null
     */
    public function before(AuthUser $user): ?bool
    {
        // 1 represents the global Super Admin role.
        // Implementation varies based on how roles are loaded on AuthUser.
        return null; 
    }
}