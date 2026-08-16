<?php
// Path: app/Core/Bootstrap/AppServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use App\Core\Events\EventRegistry;
use App\Core\Events\EventBus;

/**
 * Enterprise Service Provider: App
 * تسجيل الكيانات الأساسية للنظام لتكون متوفرة في كل مكان كـ Singletons.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // تسجيل نظام الأحداث ليكون نسخة واحدة في الذاكرة طوال دورة حياة الطلب
        $this->app->singleton(EventRegistry::class);
        $this->app->singleton(EventBus::class);
    }
}