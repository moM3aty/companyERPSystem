<?php
// Path: app/Modules/Inventory/LandedCost/Domain/LandedCostRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Domain;

use App\Core\Contracts\RepositoryInterface;

interface LandedCostRepositoryInterface extends RepositoryInterface
{
    public function bulkInsertAllocations(int $landedCostId, array $allocations): void;
}