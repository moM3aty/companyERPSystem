<?php
// Path: app/Modules/FixedAssets/Assets/Infrastructure/AssetRepository.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\FixedAssets\Assets\Domain\AssetRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Asset
 */
class AssetRepository extends BaseRepository implements AssetRepositoryInterface
{
    protected string $table = 'fixed_assets';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function codeExists(string $assetCode, int $companyId): bool
    {
        $result = $this->newQuery()
            ->where('asset_code', '=', $assetCode)
            ->where('company_id', '=', $companyId)
            ->first();

        return $result !== null;
    }
}