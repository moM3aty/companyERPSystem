<?php
// Path: app/Modules/Treasury/Reconciliation/Domain/BankStatementRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Domain;

use App\Core\Contracts\RepositoryInterface;

interface BankStatementRepositoryInterface extends RepositoryInterface
{
    public function bulkInsertLines(int $statementId, array $lines): void;
    
    /**
     * جلب السطور غير المطابقة (Unmatched) لعمل مطابقة آلية عليها.
     */
    public function getUnmatchedLines(int $statementId): array;
}