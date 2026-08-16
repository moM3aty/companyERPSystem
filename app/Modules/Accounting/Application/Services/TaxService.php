<?php
// Path: app/Modules/Accounting/Application/Services/TaxService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services;

use App\Modules\Accounting\Domain\Repositories\TaxRepositoryInterface;

class TaxService
{
    public function __construct(
        private readonly TaxRepositoryInterface $taxRepository
    ) {}

    public function getActiveTaxes(int $companyId): array
    {
        return $this->taxRepository->getAllActive($companyId);
    }
}