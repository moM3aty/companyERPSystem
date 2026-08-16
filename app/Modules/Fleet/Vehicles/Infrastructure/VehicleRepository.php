<?php
// Path: app/Modules/Fleet/Vehicles/Infrastructure/VehicleRepository.php

declare(strict_types=1);

namespace App\Modules\Fleet\Vehicles\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Fleet\Vehicles\Domain\VehicleRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Vehicle
 */
class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    protected string $table = 'fleet_vehicles';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function plateExists(string $plateNumber, int $companyId): bool
    {
        $result = $this->newQuery()
            ->where('plate_number', '=', $plateNumber)
            ->where('company_id', '=', $companyId)
            ->first();

        return $result !== null;
    }
}