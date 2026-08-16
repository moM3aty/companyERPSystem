<?php
// Path: app/Modules/FixedAssets/Assets/Domain/AssetRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Asset
 */
interface AssetRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق من كود الأصل لتجنب التكرار.
     *
     * @param string $assetCode
     * @param int $companyId
     * @return bool
     */
    public function codeExists(string $assetCode, int $companyId): bool;
}