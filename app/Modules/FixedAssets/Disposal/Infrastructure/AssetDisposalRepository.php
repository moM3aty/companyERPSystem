<?php
// Path: app/Modules/FixedAssets/Disposal/Infrastructure/AssetDisposalRepository.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Disposal\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class AssetDisposalRepository extends BaseRepository
{
    protected string $table = 'asset_disposals';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}