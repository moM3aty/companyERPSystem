<?php
// Path: app/Modules/Consolidation/Services/EliminationService.php

declare(strict_types=1);

namespace App\Modules\Consolidation\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Application Service: Elimination Engine
 * محرك مالي معقد يقوم بالبحث عن المعاملات المتبادلة بين الشركات التابعة (Intercompany Transactions)
 * ويقوم بإنشاء قيود استبعاد (Eliminations) لخصمها من القوائم المجمعة آلياً.
 */
class EliminationService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->logger = $logger;
    }

    /**
     * احتساب وإصدار قيود الاستبعاد لمجموعة قابضة لفترة محددة.
     *
     * @param int $groupId
     * @param int $year
     * @param int $month
     * @return int عدد الاستبعادات التي تمت
     */
    public function processEliminations(int $groupId, int $year, int $month): int
    {
        return $this->transaction->execute(function () use ($groupId, $year, $month) {
            $eliminationsCount = 0;
            $startDate = "{$year}-" . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . "-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // جلب الشركات التابعة للمجموعة
            $entities = $this->db->connection()->select(
                "SELECT company_id FROM consolidation_entities WHERE consolidation_group_id = ? AND is_active = 1",
                [$groupId]
            );

            if (empty($entities)) return 0;

            $companyIds = array_column($entities, 'company_id');
            $placeholders = implode(',', array_fill(0, count($companyIds), '?'));
            $bindings = array_merge($companyIds, $companyIds, [$startDate, $endDate]);

            // البحث عن العمليات المتبادلة (Intercompany) المُرحلة بين شركات المجموعة
            $sql = "SELECT * FROM intercompany_transactions 
                    WHERE from_company_id IN ({$placeholders}) 
                      AND to_company_id IN ({$placeholders}) 
                      AND status = 'posted' 
                      AND created_at BETWEEN ? AND ?";

            $transactions = $this->db->connection()->select($sql, $bindings);

            foreach ($transactions as $txn) {
                // تسجيل قيد الاستبعاد
                $this->db->connection()->insert(
                    "INSERT INTO elimination_entries 
                     (consolidation_group_id, period_year, period_month, from_company_id, to_company_id, elimination_amount, reason, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $groupId,
                        $year,
                        $month,
                        $txn['from_company_id'],
                        $txn['to_company_id'],
                        $txn['amount'],
                        "Auto-elimination for TXN: {$txn['transaction_no']}",
                        date('Y-m-d H:i:s')
                    ]
                );
                $eliminationsCount++;
            }

            $this->logger->info("Elimination Engine: Processed {$eliminationsCount} eliminations for Group ID {$groupId} ({$year}-{$month}).");

            return $eliminationsCount;
        });
    }
}