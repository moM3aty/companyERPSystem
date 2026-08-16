<?php
// Path: app/Modules/SupplyChain/Services/ForecastService.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Demand Forecasting
 * يولد تنبؤات المبيعات المستقبلية بناءً على خوارزمية (Moving Average) لمبيعات الأشهر السابقة.
 */
class ForecastService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    public function generateForecast(int $companyId, int $monthsLookback = 3): int
    {
        return $this->transaction->execute(function () use ($companyId, $monthsLookback) {
            
            // حساب متوسط المبيعات الشهرية لكل منتج في الأشهر الماضية
            $sql = "
                SELECT i.product_id, SUM(i.quantity) as total_sold
                FROM sales_invoice_items i
                JOIN sales_invoices inv ON i.sales_invoice_id = inv.id
                WHERE inv.company_id = ? 
                  AND inv.status = 'posted'
                  AND inv.invoice_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? MONTH)
                GROUP BY i.product_id
            ";

            $salesData = $this->db->connection()->select($sql, [$companyId, $monthsLookback]);
            
            $forecastsCreated = 0;
            $targetMonth = date('Y-m-01', strtotime('+1 month')); // التنبؤ للشهر القادم

            foreach ($salesData as $data) {
                $movingAverage = (float)$data['total_sold'] / $monthsLookback;
                
                // حفظ التنبؤ (Demand Forecast)
                $this->db->connection()->insert(
                    "INSERT INTO supply_chain_demand_forecasts 
                    (company_id, product_id, forecast_month, forecasted_quantity, calculation_method, created_at) 
                    VALUES (?, ?, ?, ?, 'moving_average', ?)",
                    [$companyId, $data['product_id'], $targetMonth, ceil($movingAverage), date('Y-m-d H:i:s')]
                );
                
                $forecastsCreated++;
            }

            return $forecastsCreated;
        });
    }
}