<?php
// Path: routes/core.php

declare(strict_types=1);

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Core\MasterData\Http\Controllers\MasterDataController;
use App\Core\Approval\Http\Controllers\ApprovalController;
use App\Core\Audit\Http\Controllers\AuditLogController;
use App\Core\Notifications\Http\Controllers\NotificationController;

/**
 * Enterprise Core Routes
 * هذا الملف يتم تسجيله في الـ RouteServiceProvider. يحتوي على المسارات المركزية للنظام.
 */
return static function (Router $router): void {
    
    $core = new RouteGroup($router, [
        'prefix'     => 'api/v1/core',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $core->group(function (RouteGroup $group) {
        
        // Master Data (Dropdowns & Lookups)
        $group->get('/master-data/currencies', [MasterDataController::class, 'currencies']);
        $group->get('/master-data/countries', [MasterDataController::class, 'countries']);
        $group->get('/master-data/lookups/{type}', [MasterDataController::class, 'lookups']);
        $group->get('/master-data/exchange-rate', [MasterDataController::class, 'exchangeRate']);

        // Workflows & Approvals
        $group->get('/approvals/pending', [ApprovalController::class, 'pending']);
        $group->post('/approvals/{id}/decide', [ApprovalController::class, 'decide']);

        // Audit Trails
        $group->get('/audit/{entityType}/{entityId}/timeline', [AuditLogController::class, 'entityTimeline']);
        
        // Notifications (Note: We override middleware here slightly internally if needed, but tenant is fine)
        $group->get('/notifications', [NotificationController::class, 'index']);
        $group->put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        $group->put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
};