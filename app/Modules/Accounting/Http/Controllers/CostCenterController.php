<?php
// Path: app/Modules/Accounting/Http/Controllers/CostCenterController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Modules\Accounting\Application\Services\CostCenterService;

class CostCenterController
{
    public function __construct(
        private readonly CostCenterService $costCenterService
    ) {}

    public function index(Request $request): void
    {
        $companyId = 1;
        
        $costCenters = $this->costCenterService->getAllCostCenters($companyId);

        require BASE_PATH . '/resources/views/accounting/cost-centers/index.php';
    }
}