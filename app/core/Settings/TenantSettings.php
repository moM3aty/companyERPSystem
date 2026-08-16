<?php
// Path: app/Core/Settings/TenantSettings.php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Tenant\TenantContext;

/**
 * Enterprise Tenant Settings Base
 * كلاس أساسي (Abstract) يربط الإعدادات بسياق الـ Tenant (الشركة أو الفرع).
 * يضمن عدم إمكانية تداخل إعدادات شركة مع شركة أخرى أمنياً.
 */
abstract class TenantSettings
{
    protected SettingsManager $manager;
    protected TenantContext $tenantContext;

    /**
     * TenantSettings constructor.
     *
     * @param SettingsManager $manager
     * @param TenantContext $tenantContext
     */
    public function __construct(SettingsManager $manager, TenantContext $tenantContext)
    {
        $this->manager = $manager;
        $this->tenantContext = $tenantContext;
    }


    /**
     * يحدد نطاق الإعداد (Company أو Branch). يجب تنفيذه في الكلاسات الوارثة.
     *
     * @return string
     */
    abstract protected function getScope(): string;

    /**
     * يجلب المعرف الآمن للـ Tenant. يجب تنفيذه في الكلاسات الوارثة.
     *
     * @return int
     * @throws \App\Core\Exceptions\BusinessException
     */
    abstract protected function getScopeId(): int;

    /**
     * جلب إعداد محدد للشركة/الفرع.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->manager->get($this->getScope(), $this->getScopeId(), $key, $default);
    }

    /**
     * حفظ إعداد محدد للشركة/الفرع.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        $this->manager->set($this->getScope(), $this->getScopeId(), $key, $value, $type);
    }

    /**
     * جلب كل الإعدادات دفعة واحدة.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->manager->loadScope($this->getScope(), $this->getScopeId());
    }
}