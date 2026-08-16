<?php
// Path: bootstrap/app.php

declare(strict_types=1);

use App\Core\Bootstrap\Container;

// 1. إنشاء الحاوية الرئيسية
$app = new Container(BASE_PATH);

// 2. تسجيل الحاوية كـ Singleton
$app->singleton(Container::class, function () use ($app) {
    return $app;
});

// 3. تسجيل وتشغيل مزودي الخدمات
$providers = [
    \App\Core\Bootstrap\RouteServiceProvider::class,
    \App\Core\Bootstrap\RepositoryServiceProvider::class,
    \App\Core\Bootstrap\EventServiceProvider::class,
    \App\Core\Bootstrap\AuthServiceProvider::class,
];

foreach ($providers as $providerClass) {
    if (!class_exists($providerClass)) {
        continue;
    }

    $provider = new $providerClass($app);

    if (method_exists($provider, 'register')) {
        $provider->register();
    }

    if (method_exists($provider, 'boot')) {
        $provider->boot();
    }
}

return $app;