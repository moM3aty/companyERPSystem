    <?php
// Path: app/Modules/Accounting/Application/DTOs/ReconciliationDTO.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs;

/**
 * Enterprise DTO: Bank Reconciliation
 * ينقل بيانات مطابقة البنك.
 */
class ReconciliationDTO
{
    /**
     * @param int $companyId
     * @param int $bankAccountId
     * @param string $statementDate
     * @param float $statementEndingBalance
     * @param array<int> $matchedJournalLineIds
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $bankAccountId,
        public readonly string $statementDate,
        public readonly float $statementEndingBalance,
        public readonly array $matchedJournalLineIds
    ) {}
}