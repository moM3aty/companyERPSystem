<?php
// Path: app/Core/Authorization/AccessScope.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Database\QueryBuilder;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Access Scope (Row-Level Security)
 * يضمن ألا يتمكن الموظف من رؤية بيانات موظفين آخرين في الجداول المشتركة 
 * (مثال: موظف مبيعات يرى فواتيره فقط، بينما مديره يرى فواتير القسم بالكامل).
 */
class AccessScope
{
    /**
     * تطبيق الفلتر الأمني على استعلام قاعدة البيانات.
     */
    public static function apply(QueryBuilder $query, AuthUser $user, string $ownershipColumn = 'created_by'): QueryBuilder
    {
        // إذا كان المستخدم لا يملك صلاحية الرؤية الشاملة (View All)، نحجم نطاقه لرؤية بياناته فقط.
        // يتم التأكد من ذلك عبر فحص سريع من الـ Gate في الكنترولر، وبناءً عليه يستدعى هذا السكوب.
        
        return $query->where($ownershipColumn, '=', $user->id);
    }
    
    /**
     * تطبيق فلتر على مستوى الفروع.
     */
    public static function applyBranchScope(QueryBuilder $query, int $branchId, string $branchColumn = 'branch_id'): QueryBuilder
    {
        return $query->where($branchColumn, '=', $branchId);
    }
}