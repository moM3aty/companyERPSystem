<?php
// Path: app/Modules/Purchasing/Returns/Domain/DebitNoteRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Domain;

use App\Core\Contracts\RepositoryInterface;

interface DebitNoteRepositoryInterface extends RepositoryInterface
{
    public function generateDebitNoteNumber(int $companyId): string;
    public function bulkInsertItems(int $debitNoteId, array $items): void;
}