<?php
// Path: app/Modules/Fleet/Trips/Domain/TripRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Fleet Trip
 */
interface TripRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كانت المركبة في رحلة نشطة حالياً.
     *
     * @param int $vehicleId
     * @param int $companyId
     * @return bool
     */
    public function hasActiveTrip(int $vehicleId, int $companyId): bool;
}