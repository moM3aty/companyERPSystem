<?php
// Path: app/Modules/Intercompany/Services/ReconciliationService.php
declare(strict_types=1);

namespace App\Modules\Intercompany\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

class ReconciliationService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected MatchingService $matchingService;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, MatchingService $matchingService)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->matchingService = $matchingService;
    }

    public function generateReconciliationDocument(int $periodId, int $companyA, int $companyB, int $userId): int
    {
        return $this->transaction->execute(function () use ($periodId, $companyA, $companyB, $userId) {
            $matchResult = $this->matchingService->runMatching($periodId, $companyA, $companyB);
            
            $variance = $matchResult['variance'];
            $status = $variance == 0 ? 'matched' : 'has_variance';

            $this->db->connection()->insert(
                "INSERT INTO intercompany_reconciliations 
                (period_id, company_a_id, company_b_id, total_ar_company_a, total_ap_company_b, variance_amount, status, reconciliation_date, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $periodId, $companyA, $companyB, 
                    $matchResult['total_ar'], $matchResult['total_ap'], 
                    $variance, $status, date('Y-m-d'), $userId, date('Y-m-d H:i:s')
                ]
            );

            return (int) $this->db->connection()->getPdo()->lastInsertId();
        });
    }
}