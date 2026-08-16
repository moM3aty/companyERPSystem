<?php
// Path: app/Modules/HR/Performance/Infrastructure/AppraisalRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Performance\Domain\AppraisalRepositoryInterface;

class AppraisalRepository extends BaseRepository implements AppraisalRepositoryInterface
{
    protected string $table = 'hr_appraisals';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function hasOverlappingAppraisal(int $employeeId, string $periodStart, string $periodEnd, int $companyId): bool
    {
        $sql = "SELECT id FROM {$this->table} 
                WHERE employee_id = ? AND company_id = ? 
                  AND period_start <= ? AND period_end >= ? LIMIT 1";

        $result = $this->db->connection()->selectOne($sql, [$employeeId, $companyId, $periodEnd, $periodStart]);

        return $result !== null;
    }
}