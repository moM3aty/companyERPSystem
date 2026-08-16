<?php
// Path: app/Modules/FixedAssets/Transfers/Infrastructure/AssetTransferRepository.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Transfers\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class AssetTransferRepository extends BaseRepository
{
    protected string $table = 'asset_transfers';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}