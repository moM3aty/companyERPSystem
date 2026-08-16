<?php
// Path: app/Core/Settings/ModuleSettings.php

declare(strict_types=1);

namespace App\Core\Settings;

/**
 * Enterprise Module Settings
 * يتيح تجميع الإعدادات داخل سياق الشركة تحت اسم الموديول (مثال: إعدادات موديول المبيعات فقط).
 * يوفر كوداً أنظف في الـ Controllers.
 */
class ModuleSettings
{
    protected CompanySettings $companySettings;
    protected string $modulePrefix;

    /**
     * ModuleSettings constructor.
     *
     * @param CompanySettings $companySettings
     */
    public function __construct(CompanySettings $companySettings)
    {
        $this->companySettings = $companySettings;
    }


    /**
     * تعيين الموديول النشط (مثال: 'sales' أو 'inventory').
     *
     * @param string $module
     * @return self
     */
    public function forModule(string $module): self
    {
        $this->modulePrefix = trim($module, '.') . '.';
        return $this;
    }

    /**
     * جلب إعداد داخل الموديول.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureModuleIsSet();
        return $this->companySettings->get($this->modulePrefix . $key, $default);
    }

    /**
     * تحديث إعداد داخل الموديول.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        $this->ensureModuleIsSet();
        $this->companySettings->set($this->modulePrefix . $key, $value, $type);
    }

    /**
     * التحقق الداخلي من تعيين اسم الموديول.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function ensureModuleIsSet(): void
    {
        if (empty($this->modulePrefix)) {
            throw new \RuntimeException("Module prefix must be set using forModule() before accessing settings.");
        }
    }
}