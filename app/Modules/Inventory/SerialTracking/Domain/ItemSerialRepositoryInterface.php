<?php
// Path: app/Modules/Inventory/SerialTracking/Domain/ItemSerialRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\SerialTracking\Domain;

use App\Core\Contracts\RepositoryInterface;

interface ItemSerialRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان السيريال موجوداً مسبقاً لهذا الصنف لمنع التكرار (Unique).
     */
    public function existsForProduct(string $serialNumber, int $productId, int $companyId): bool;
}