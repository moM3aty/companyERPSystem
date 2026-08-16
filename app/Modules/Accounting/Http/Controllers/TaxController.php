<?php
// Path: app/Modules/Accounting/Http/Controllers/TaxController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Modules\Accounting\Application\Services\TaxService;

class TaxController
{
    public function __construct(
        private readonly TaxService $taxService
    ) {}

    public function index(Request $request): void
    {
        $companyId = 1;
        
        $taxes = $this->taxService->getActiveTaxes($companyId);

        require BASE_PATH . '/resources/views/accounting/taxes/index.php';
    }
}