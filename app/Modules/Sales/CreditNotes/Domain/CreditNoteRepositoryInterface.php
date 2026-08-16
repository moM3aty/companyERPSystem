<?php
// Path: app/Modules/Sales/CreditNotes/Domain/CreditNoteRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Domain;

use App\Core\Contracts\RepositoryInterface;

interface CreditNoteRepositoryInterface extends RepositoryInterface
{
    public function generateCreditNoteNumber(int $companyId): string;
    public function bulkInsertItems(int $creditNoteId, array $items): void;
}