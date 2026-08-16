<?php
// Path: app/Core/Authorization/PolicyResolver.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Auth\AuthUser;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Policy Resolver
 * يربط بين الـ (Gate) وبين كلاسات (Policies) مخصصة لفحص القواعد التجارية المعقدة.
 * مثال: هل يسمح لهذا المستخدم بتعديل "هذه الفاتورة بالذات"؟ (تعتمد على حالة الفاتورة أو مالكها).
 */
class PolicyResolver
{
    protected array $policies = [];

    /**
     * تسجيل Policy لمورد معين.
     *
     * @param string $resource
     * @param string $policyClass
     * @return void
     */
    public function register(string $resource, string $policyClass): void
    {
        $this->policies[$resource] = $policyClass;
    }

    /**
     * تنفيذ السياسة الأمنية.
     *
     * @param AuthUser $user
     * @param string $resource
     * @param string $action
     * @param mixed $model كائن البيانات (مثال: Invoice Object)
     * @return bool
     * @throws AuthorizationException
     */
    public function check(AuthUser $user, string $resource, string $action, mixed $model = null): bool
    {
        if (!isset($this->policies[$resource])) {
            return true; // إذا لم تكن هناك سياسة معقدة، نعتمد على الصلاحيات العادية فقط
        }

        $policyClass = $this->policies[$resource];
        $policy = new $policyClass();

        if (!method_exists($policy, $action)) {
            throw new AuthorizationException("Policy action [{$action}] not defined for resource [{$resource}].");
        }

        return $policy->$action($user, $model);
    }
}