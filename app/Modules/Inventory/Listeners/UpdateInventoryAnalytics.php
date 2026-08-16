<?php
// Path: app/Modules/Inventory/Listeners/UpdateInventoryAnalytics.php

declare(strict_types=1);

namespace App\Modules\Inventory\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\StockMovements\Domain\Events\StockUpdatedEvent;

/**
 * Enterprise Listener: Update Inventory Analytics
 * يعمل في الخلفية (Background) لتحديث الجداول الإحصائية (OLAP/Fact Tables) المخصصة للـ Dashboard
 * لمنع بطء النظام عند طلب تقارير المخزون.
 */
class UpdateInventoryAnalytics implements EventListener
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof StockUpdatedEvent) {
            return;
        }

        $date = date('Y-m-d');
        $companyId = $event->companyId;
        $warehouseId = $event->warehouseId;

        // تحديث إحصائيات الحركات اليومية للمستودع (Upsert)
        $sql = "INSERT INTO inventory_daily_stats (company_id, warehouse_id, stat_date, total_movements, last_updated) 
                VALUES (?, ?, ?, 1, ?) 
                ON DUPLICATE KEY UPDATE total_movements = total_movements + 1, last_updated = VALUES(last_updated)";

        $this->db->connection()->statement($sql, [
            $companyId, $warehouseId, $date, date('Y-m-d H:i:s')
        ]);
    }
}