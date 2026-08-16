<?php
// Path: app/Modules/Maintenance/SpareParts/Infrastructure/SparePartRepository.php

declare(strict_types=1);

namespace App\Modules\Maintenance\SpareParts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class SparePartRepository extends BaseRepository
{
    protected string $table = 'maintenance_spare_parts';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}