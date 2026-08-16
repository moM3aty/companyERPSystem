<?php
// Path: app/Modules/Administration/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Administration\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Administration\Controllers\DashboardController;
use App\Modules\Administration\Controllers\SystemHealthController;

/**
 * Enterprise Administration Routes
 * يتم تحميل هذا الملف آلياً بواسطة الـ RouteServiceProvider.
 */
return static function (Router $router): void {
    
    // مجموعة المسارات الخاصة بموديول الإدارة المحمية بالـ Tenant Scope
    $admin = new RouteGroup($router, [
        'prefix'     => 'api/v1/administration',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $admin->group(function (RouteGroup $group) {
        
        // Dashboard & Metrics
        $group->get('/dashboard', [DashboardController::class, 'index']);
        
        // System Health (For DevOps & IT Admins)
        $group->get('/system-health', [SystemHealthController::class, 'check']);
        
        // (مسارات الـ Users و הـ Roles يتم إضافتها هنا تباعاً...)
    });
};