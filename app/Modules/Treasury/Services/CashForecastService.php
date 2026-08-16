<?php
// Path: app/Modules/Treasury/Services/CashForecastService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Cash Flow Forecast (التنبؤ النقدي)
 * خوارزمية مؤسسية تحسب التدفقات النقدية المستقبلية (Inflows & Outflows) 
 * بناءً على الفواتير المفتوحة (العملاء والموردين) وتواريخ استحقاقها.
 */
class CashForecastService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * توليد تقرير التنبؤ بالسيولة النقدية.
     *
     * @param int $companyId
     * @param int $daysAhead عدد الأيام المستقبلية للمراقبة
     * @return array
     */
    public function generateForecast(int $companyId, int $daysAhead = 30): array
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$daysAhead} days"));

        // 1. جلب النقدية المتاحة حالياً في جميع البنوك والصناديق (Current Liquidity)
        $liquidityRow = $this->db->connection()->selectOne("
            SELECT SUM(current_balance) as total_cash 
            FROM treasury_bank_accounts WHERE company_id = ? AND is_active = 1
        ", [$companyId]);
        
        $currentLiquidity = (float) ($liquidityRow['total_cash'] ?? 0.0);

        // 2. التدفقات الداخلة المتوقعة (Accounts Receivable - فواتير عملاء غير مسددة)
        $inflowSql = "
            SELECT due_date, SUM(grand_total - paid_amount) as amount 
            FROM sales_invoices 
            WHERE company_id = ? AND status = 'posted' 
              AND due_date BETWEEN ? AND ?
            GROUP BY due_date ORDER BY due_date ASC
        ";
        $inflows = $this->db->connection()->select($inflowSql, [$companyId, $startDate, $endDate]);

        // 3. التدفقات الخارجة المتوقعة (Accounts Payable - فواتير موردين غير مسددة)
        $outflowSql = "
            SELECT due_date, SUM(grand_total - paid_amount) as amount 
            FROM purchase_invoices 
            WHERE company_id = ? AND status = 'posted' 
              AND due_date BETWEEN ? AND ?
            GROUP BY due_date ORDER BY due_date ASC
        ";
        $outflows = $this->db->connection()->select($outflowSql, [$companyId, $startDate, $endDate]);

        return $this->formatTimeline($currentLiquidity, $inflows, $outflows, $startDate, $endDate);
    }

    /**
     * بناء خط زمني (Timeline) متواصل يوم بيوم للرصيد المتوقع.
     */
    protected function formatTimeline(float $currentLiquidity, array $inflows, array $outflows, string $startDate, string $endDate): array
    {
        $timeline = [];
        $runningBalance = $currentLiquidity;

        // تحويل المصفوفات لبحث سريع [date => amount]
        $inMap = array_column($inflows, 'amount', 'due_date');
        $outMap = array_column($outflows, 'amount', 'due_date');

        $current = strtotime($startDate);
        $end = strtotime($endDate);

        while ($current <= $end) {
            $dateStr = date('Y-m-d', $current);
            
            $in = (float) ($inMap[$dateStr] ?? 0.0);
            $out = (float) ($outMap[$dateStr] ?? 0.0);
            
            $runningBalance += ($in - $out);

            $timeline[] = [
                'date'             => $dateStr,
                'expected_inflow'  => round($in, 2),
                'expected_outflow' => round($out, 2),
                'projected_balance'=> round($runningBalance, 2),
            ];

            $current = strtotime('+1 day', $current);
        }

        return [
            'current_liquidity' => round($currentLiquidity, 2),
            'timeline' => $timeline
        ];
    }
}