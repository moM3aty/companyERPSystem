<?php
// Path: app/Modules/Fleet/Repositories/DriverRepository.php
declare(strict_types=1);

namespace App\Modules\Fleet\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class DriverRepository extends BaseRepository
{
    protected string $table = 'fleet_drivers';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}