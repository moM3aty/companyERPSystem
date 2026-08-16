<?php
// Path: app/Modules/Fleet/Trips/Infrastructure/TripRepository.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Fleet\Trips\Domain\TripRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Fleet Trip
 */
class TripRepository extends BaseRepository implements TripRepositoryInterface
{
    protected string $table = 'fleet_trips';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // سجلات الرحلات تعتبر Immutable بعد إنشائها

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function hasActiveTrip(int $vehicleId, int $companyId): bool
    {
        $result = $this->newQuery()
            ->where('vehicle_id', '=', $vehicleId)
            ->where('company_id', '=', $companyId)
            ->where('status', '=', 'in_progress')
            ->first();

        return $result !== null;
    }
}