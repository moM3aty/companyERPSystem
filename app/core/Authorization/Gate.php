<?php
// Path: app/Core/Authorization/Gate.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Auth\AuthManager;
use App\Core\Tenant\TenantContext;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Authorization Gate
 * البوابة الرئيسية التي يتفاعل معها المطورون داخل الـ Controllers للتحقق من الصلاحيات.
 */
class Gate
{
    protected AuthManager $auth;
    protected TenantContext $tenant;
    protected PermissionChecker $checker;

    /**
     * Gate constructor.
     *
     * @param AuthManager $auth
     * @param TenantContext $tenant
     * @param PermissionChecker $checker
     */
    public function __construct(AuthManager $auth, TenantContext $tenant, PermissionChecker $checker)
    {
        $this->auth = $auth;
        $this->tenant = $tenant;
        $this->checker = $checker;
    }

    /**
     * التحقق بسلاسة مما إذا كان المستخدم يمتلك صلاحية لعمل معين (يُرجع True/False).
     *
     * @param string $module الوحدة (مثال: sales, hr, inventory)
     * @param string $resource المورد (مثال: invoice, employee, stock)
     * @param string $action الإجراء (مثال: create, view, update, delete)
     * @return bool
     */
    public function allows(string $module, string $resource, string $action): bool
    {
        $user = $this->auth->user();
        
        // لا يوجد مستخدم مسجل دخول = لا توجد صلاحيات
        if (!$user) {
            return false;
        }

        // الاعتماد على TenantContext لضمان أننا نفحص الصلاحيات داخل نطاق الشركة الحالية فقط
        $companyId = $this->tenant->getCompanyId() ?? $user->companyId;

        return $this->checker->hasPermission($user->id, $companyId, $module, $resource, $action);
    }

    /**
     * فرض الصلاحية بقوة. إذا لم يمتلك المستخدم الصلاحية، يتم إيقاف العملية ورمي استثناء 403.
     * تستخدم عادة في بداية الدوال في الـ Controllers.
     *
     * @param string $module
     * @param string $resource
     * @param string $action
     * @return void
     * @throws AuthorizationException
     */
    public function authorize(string $module, string $resource, string $action): void
    {
        if (!$this->allows($module, $resource, $action)) {
            throw new AuthorizationException(
                "Access Denied: Missing required permission [{$module}.{$resource}.{$action}]. Contact your administrator."
            );
        }
    }
}