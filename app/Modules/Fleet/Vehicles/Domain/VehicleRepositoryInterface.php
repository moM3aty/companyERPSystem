<?php
// Path: app/Modules/Fleet/Vehicles/Domain/VehicleRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Fleet\Vehicles\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Vehicle
 */
interface VehicleRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق من عدم تكرار رقم اللوحة داخل نفس الشركة.
     *
     * @param string $plateNumber
     * @param int $companyId
     * @return bool
     */
    public function plateExists(string $plateNumber, int $companyId): bool;
}