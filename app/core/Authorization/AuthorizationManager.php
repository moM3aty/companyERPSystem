<?php
// Path: app/Core/Authorization/AuthorizationManager.php

declare(strict_types=1);

namespace App\Core\Authorization;

/**
 * Enterprise Authorization Manager (Facade)
 * الواجهة المركزية الشاملة للتحكم بجميع تفاصيل الصلاحيات والأدوار في النظام.
 */
class AuthorizationManager
{
    public readonly Gate $gate;
    public readonly RoleManager $roles;
    public readonly PermissionManager $permissions;
    public readonly PolicyResolver $policies;

    public function __construct(
        Gate $gate, 
        RoleManager $roles, 
        PermissionManager $permissions, 
        PolicyResolver $policies
    ) {
        $this->gate = $gate;
        $this->roles = $roles;
        $this->permissions = $permissions;
        $this->policies = $policies;
    }
}