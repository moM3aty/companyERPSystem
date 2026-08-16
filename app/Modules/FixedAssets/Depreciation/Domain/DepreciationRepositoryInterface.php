<?php
// Path: app/Modules/FixedAssets/Depreciation/Domain/DepreciationRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Depreciation Record
 */
interface DepreciationRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان الأصل قد تم إهلاكه في هذا الشهر المالي مسبقاً لمنع التكرار.
     *
     * @param int $assetId
     * @param int $year
     * @param int $month
     * @return bool
     */
    public function isDepreciatedForPeriod(int $assetId, int $year, int $month): bool;
}