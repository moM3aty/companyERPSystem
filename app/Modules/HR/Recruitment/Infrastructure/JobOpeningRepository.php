<?php
// Path: app/Modules/HR/Recruitment/Infrastructure/JobOpeningRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Recruitment\Domain\JobOpeningRepositoryInterface;

class JobOpeningRepository extends BaseRepository implements JobOpeningRepositoryInterface
{
    protected string $table = 'hr_job_openings';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}