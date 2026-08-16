<?php
// Path: app/Modules/Inventory/Jobs/GenerateStockValuationJob.php

declare(strict_types=1);

namespace App\Modules\Inventory\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Background Job: Generate Stock Valuation
 * يتم جدولته ليعمل في نهاية كل شهر (Cron Job) لتسجيل وتوثيق قيمة المخزون الحالية كـ Snapshot
 * لاستخدامه في التقارير المالية والضريبية.
 */
class GenerateStockValuationJob extends Job
{
    public readonly int $companyId;

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        $period = date('Y-m');

        // جلب القيمة الإجمالية
        $sql = "SELECT SUM(quantity * average_cost) as total_value 
                FROM inventory_stocks 
                WHERE company_id = ? AND quantity > 0";
                
        $result = $db->connection()->selectOne($sql, [$this->companyId]);
        $totalValue = (float) ($result['total_value'] ?? 0.0);

        // توثيق القيمة
        $db->connection()->insert(
            "INSERT INTO inventory_valuation_history (company_id, period, total_value, created_at) VALUES (?, ?, ?, ?)",
            [$this->companyId, $period, $totalValue, date('Y-m-d H:i:s')]
        );
    }
}