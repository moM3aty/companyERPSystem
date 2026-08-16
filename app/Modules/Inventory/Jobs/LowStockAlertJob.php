<?php
// Path: app/Modules/Inventory/Jobs/LowStockAlertJob.php

declare(strict_types=1);

namespace App\Modules\Inventory\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;
use App\Core\Notifications\NotificationManager;

/**
 * Enterprise Job: Low Stock Alert
 * يعمل في الـ Background (Queue) لفحص الأرصدة مقابل (Reorder Point)، وإرسال تنبيهات لمدراء المشتريات.
 */
class LowStockAlertJob extends Job
{
    public readonly int $companyId;

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
        $this->retryPolicy = new \App\Core\Queue\RetryPolicy(2, 60);
    }

    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        
        /** @var NotificationManager $notifier */
        $notifier = $container->make(NotificationManager::class);

        // جلب المنتجات التي وصل رصيدها للحد الأدنى (Reorder Point)
        $sql = "
            SELECT s.product_id, p.name, s.quantity, r.min_quantity 
            FROM inventory_stocks s
            JOIN inventory_reorder_rules r ON s.product_id = r.product_id AND s.warehouse_id = r.warehouse_id
            JOIN products p ON s.product_id = p.id
            WHERE s.company_id = ? AND s.quantity <= r.min_quantity
        ";

        $alerts = $db->connection()->select($sql, [$this->companyId]);

        if (empty($alerts)) {
            return;
        }

        // جلب مدراء المشتريات والمخازن لإرسال الإشعار
        $managers = $db->connection()->select(
            "SELECT u.id FROM users u 
             JOIN user_roles ur ON u.id = ur.user_id
             JOIN roles r ON ur.role_id = r.id
             WHERE u.company_id = ? AND r.name IN ('Inventory Manager', 'Purchasing Manager') AND u.is_active = 1",
            [$this->companyId]
        );

        foreach ($alerts as $alert) {
            foreach ($managers as $manager) {
                $notifier->send(
                    (int) $manager['id'],
                    'low_stock_alert',
                    [
                        'product_name' => $alert['name'],
                        'current_qty'  => $alert['quantity'],
                        'min_qty'      => $alert['min_quantity']
                    ],
                    ['in_app', 'email']
                );
            }
        }
    }
}