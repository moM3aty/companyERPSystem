<?php
// Path: app/Modules/FixedAssets/Depreciation/Infrastructure/DepreciationRepository.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\FixedAssets\Depreciation\Domain\DepreciationRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Depreciation
 */
class DepreciationRepository extends BaseRepository implements DepreciationRepositoryInterface
{
    protected string $table = 'asset_depreciations';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial records are immutable

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function isDepreciatedForPeriod(int $assetId, int $year, int $month): bool
    {
        $result = $this->newQuery()
            ->where('asset_id', '=', $assetId)
            ->where('period_year', '=', $year)
            ->where('period_month', '=', $month)
            ->first();

        return $result !== null;
    }
}