<?php
// File 2: app/Modules/Intercompany/Services/MatchingService.php
declare(strict_types=1);

namespace App\Modules\Intercompany\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class MatchingService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * مطابقة الحركات المتبادلة بين شركتين شقيقتين واكتشاف الفروقات.
     */
    public function runMatching(int $periodId, int $companyA, int $companyB): array
    {
        // 1. جلب فواتير المبيعات الداخلية من الشركة A إلى الشركة B
        $arTransactions = $this->db->connection()->select("
            SELECT reference_number, SUM(amount) as total_ar 
            FROM intercompany_transactions 
            WHERE from_company_id = ? AND to_company_id = ? AND period_id = ? AND transaction_type = 'AR'
            GROUP BY reference_number
        ", [$companyA, $companyB, $periodId]);

        // 2. جلب فواتير المشتريات الداخلية في الشركة B من الشركة A
        $apTransactions = $this->db->connection()->select("
            SELECT reference_number, SUM(amount) as total_ap 
            FROM intercompany_transactions 
            WHERE from_company_id = ? AND to_company_id = ? AND period_id = ? AND transaction_type = 'AP'
            GROUP BY reference_number
        ", [$companyB, $companyA, $periodId]);

        $apMap = array_column($apTransactions, 'total_ap', 'reference_number');
        
        $mismatches = [];
        $totalAR = 0.0;
        $totalAP = 0.0;

        foreach ($arTransactions as $ar) {
            $ref = $ar['reference_number'];
            $arAmount = (float)$ar['total_ar'];
            $apAmount = isset($apMap[$ref]) ? (float)$apMap[$ref] : 0.0;
            
            $totalAR += $arAmount;
            $totalAP += $apAmount;

            if (round($arAmount, 2) !== round($apAmount, 2)) {
                $mismatches[] = [
                    'reference_number' => $ref,
                    'ar_amount'        => $arAmount,
                    'ap_amount'        => $apAmount,
                    'variance'         => round($arAmount - $apAmount, 2)
                ];
            }
            unset($apMap[$ref]); // إزالة ما تم مطابقته
        }

        // الحركات المسجلة في AP ولم تسجل في AR
        foreach ($apMap as $ref => $apAmount) {
            $totalAP += $apAmount;
            $mismatches[] = [
                'reference_number' => $ref,
                'ar_amount'        => 0.0,
                'ap_amount'        => $apAmount,
                'variance'         => round(0 - $apAmount, 2)
            ];
        }

        return [
            'total_ar'   => $totalAR,
            'total_ap'   => $totalAP,
            'variance'   => round($totalAR - $totalAP, 2),
            'mismatches' => $mismatches
        ];
    }
}