<?php
// Path: app/Modules/HR/Recruitment/Domain/ApplicantRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Domain;

use App\Core\Contracts\RepositoryInterface;

interface ApplicantRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان المرشح قد تقدم لهذه الوظيفة مسبقاً لمنع التكرار.
     *
     * @param int $jobOpeningId
     * @param string $email
     * @return bool
     */
    public function hasAppliedBefore(int $jobOpeningId, string $email): bool;
}