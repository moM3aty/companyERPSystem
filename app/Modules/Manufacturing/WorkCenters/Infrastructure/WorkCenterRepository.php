<?php
// Path: app/Modules/Manufacturing/WorkCenters/Infrastructure/WorkCenterRepository.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\WorkCenters\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class WorkCenterRepository extends BaseRepository
{
    protected string $table = 'manufacturing_work_centers';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}