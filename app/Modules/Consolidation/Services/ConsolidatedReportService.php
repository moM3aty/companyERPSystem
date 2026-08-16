<?php
// File 9: app/Modules/Consolidation/Services/ConsolidatedReportService.php
declare(strict_types=1);

namespace App\Modules\Consolidation\Services;

use App\Core\Database\DatabaseManager;

class ConsolidatedReportService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * إنتاج القوائم المالية المجمعة النهائية (الأرصدة المترجمة - الاستبعادات المتبادلة).
     */
    public function generateConsolidatedTrialBalance(int $periodId): array
    {
        // الاستعلام يدمج أرصدة الشركة الأم + أرصدة الشركات التابعة המترجمة + قيود الاستبعاد (Eliminations)
        $sql = "
            SELECT 
                coa.account_code,
                coa.name as account_name,
                coa.account_type,
                SUM(COALESCE(tb.translated_balance, 0)) as aggregate_balance,
                SUM(COALESCE(ee.elimination_amount, 0)) as total_eliminations,
                (SUM(COALESCE(tb.translated_balance, 0)) + SUM(COALESCE(ee.elimination_amount, 0))) as final_consolidated_balance
            FROM chart_of_accounts coa
            LEFT JOIN consolidation_translated_balances tb ON coa.id = tb.account_id AND tb.period_id = ?
            LEFT JOIN consolidation_elimination_entries ee ON coa.id = ee.account_id AND ee.period_id = ?
            GROUP BY coa.id, coa.account_code, coa.name, coa.account_type
            HAVING final_consolidated_balance <> 0
            ORDER BY coa.account_code ASC
        ";

        return $this->db->connection()->select($sql, [$periodId, $periodId]);
    }
}