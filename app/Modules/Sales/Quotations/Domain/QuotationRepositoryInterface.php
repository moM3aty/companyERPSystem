<?php
// Path: app/Modules/Sales/Quotations/Domain/QuotationRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Domain;

use App\Core\Contracts\RepositoryInterface;

interface QuotationRepositoryInterface extends RepositoryInterface
{
    public function generateQuotationNumber(int $companyId): string;
    public function bulkInsertItems(int $quotationId, array $items): void;
}