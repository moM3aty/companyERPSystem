<?php
// Path: app/Core/Bootstrap/ServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

/**
 * Base Service Provider
 * الكلاس الأب الذي ترث منه كل مزودات الخدمات في النظام.
 */
abstract class ServiceProvider
{
    protected Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * تسجيل الروابط داخل الحاوية.
     */
    abstract public function register(): void;

    /**
     * تنفيذ أي أكواد بعد اكتمال تسجيل جميع المزودات (اختياري).
     */
    public function boot(): void
    {
        // Optional
    }
}