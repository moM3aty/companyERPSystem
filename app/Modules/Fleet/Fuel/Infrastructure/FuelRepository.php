<?php
// Path: app/Modules/Fleet/Fuel/Infrastructure/FuelRepository.php

declare(strict_types=1);

namespace App\Modules\Fleet\Fuel\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class FuelRepository extends BaseRepository
{
    protected string $table = 'fleet_fuel_transactions';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}