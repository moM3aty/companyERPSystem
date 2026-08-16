<?php
// Path: app/Core/Bootstrap/DatabaseServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Service Provider: Database
 * إعداد وربط قاعدة البيانات وقراءة بيانات الاتصال من ملف .env
 */
class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // تسجيل كلاس مدير قواعد البيانات כـ Singleton لتجنب فتح اتصالات متعددة
        $this->app->singleton(DatabaseManager::class, function ($app) {
            return new DatabaseManager(
                env('DB_HOST', '127.0.0.1'),
                env('DB_DATABASE', 'nour_erp'),
                env('DB_USERNAME', 'root'),
                env('DB_PASSWORD', '')
            );
        });
    }
}