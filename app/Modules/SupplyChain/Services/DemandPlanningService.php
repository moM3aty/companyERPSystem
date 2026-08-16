<?php
// Path: app/Modules/SupplyChain/Services/DemandPlanningService.php

declare(strict_types=1);

namespace App\Modules\SupplyChain\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Demand Planning Engine
 * محرك تخطيط الطلب. يقوم بتحليل مبيعات الـ 3 أشهر الماضية، ويطبق خوارزمية (Moving Average)
 * لحساب التنبؤ بالشهر القادم وضبط الـ Safety Stock تلقائياً.
 */
class DemandPlanningService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    /**
     * تشغيل خوارزمية التنبؤ بالطلب لكل المنتجات الفعالة.
     *
     * @param int $companyId
     * @param string $targetPeriod (YYYY-MM)
     * @return int عدد التنبؤات التي تم توليدها
     */
    public function generateForecast(int $companyId, string $targetPeriod): int
    {
        return $this->transaction->execute(function () use ($companyId, $targetPeriod) {
            
            $forecastsCount = 0;
            
            // تاريخ البداية (قبل 90 يوماً من اليوم)
            $historyStart = date('Y-m-d', strtotime('-90 days'));
            $historyEnd = date('Y-m-d');

            // 1. جلب إجمالي المبيعات لكل منتج خلال الـ 90 يوماً الماضية
            $sql = "SELECT soi.product_id, SUM(soi.quantity) as total_sold
                    FROM sales_order_items soi
                    JOIN sales_orders so ON soi.sales_order_id = so.id
                    WHERE so.company_id = ? AND so.status IN ('completed', 'shipped', 'invoiced')
                      AND so.order_date BETWEEN ? AND ?
                    GROUP BY soi.product_id";

            $historicalData = $this->db->connection()->select($sql, [$companyId, $historyStart, $historyEnd]);

            foreach ($historicalData as $data) {
                $productId = (int) $data['product_id'];
                $totalSold90Days = (float) $data['total_sold'];

                // 2. تطبيق الخوارزمية (Simple Moving Average 3-Months)
                $averageMonthlyDemand = $totalSold90Days / 3;

                // 3. مسح التنبؤ القديم لنفس الفترة إن وجد، وإدخال التنبؤ الجديد
                $this->db->connection()->delete(
                    "DELETE FROM demand_forecasts WHERE company_id = ? AND product_id = ? AND forecast_period = ?",
                    [$companyId, $productId, $targetPeriod]
                );

                $this->db->connection()->insert(
                    "INSERT INTO demand_forecasts (company_id, product_id, forecast_period, expected_quantity, confidence_score, algorithm_used, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $companyId,
                        $productId,
                        $targetPeriod,
                        round($averageMonthlyDemand, 2),
                        85.00, // نسبة ثقة افتراضية (تُحسب بالانحراف المعياري في الأنظمة الأعقد)
                        'moving_average',
                        date('Y-m-d H:i:s')
                    ]
                );
                
                $forecastsCount++;
            }

            return $forecastsCount;
        });
    }
}