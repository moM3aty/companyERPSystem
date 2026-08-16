<?php
// Path: app/Modules/HR/Recruitment/Infrastructure/ApplicantRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Recruitment\Domain\ApplicantRepositoryInterface;

class ApplicantRepository extends BaseRepository implements ApplicantRepositoryInterface
{
    protected string $table = 'hr_job_applicants';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function hasAppliedBefore(int $jobOpeningId, string $email): bool
    {
        $result = $this->newQuery()
            ->where('job_opening_id', '=', $jobOpeningId)
            ->where('email', '=', strtolower(trim($email)))
            ->first();

        return $result !== null;
    }
}