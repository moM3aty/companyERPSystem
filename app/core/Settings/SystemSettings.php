<?php
// Path: app/Core/Settings/SystemSettings.php

declare(strict_types=1);

namespace App\Core\Settings;

/**
 * Enterprise System Settings Facade
 * واجهة متخصصة لإدارة الإعدادات العامة للنظام بالكامل (Global Scope).
 * مثال: (إعدادات الـ SMTP، بوابات الدفع الافتراضية، الصيانة).
 */
class SystemSettings
{
    protected SettingsManager $manager;
    protected const SCOPE = Setting::SCOPE_GLOBAL;

    /**
     * SystemSettings constructor.
     *
     * @param SettingsManager $manager
     */
    public function __construct(SettingsManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * جلب إعداد نظام عام.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->manager->get(self::SCOPE, null, $key, $default);
    }

    /**
     * تعيين إعداد نظام عام.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        $this->manager->set(self::SCOPE, null, $key, $value, $type);
    }

    /**
     * حذف إعداد نظام.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        $this->manager->forget(self::SCOPE, null, $key);
    }

    /**
     * جلب جميع إعدادات النظام كـ Array.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->manager->loadScope(self::SCOPE, null);
    }
}