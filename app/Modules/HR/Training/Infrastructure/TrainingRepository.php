<?php
// Path: app/Modules/HR/Training/Infrastructure/TrainingRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Training\Domain\TrainingRepositoryInterface;

class TrainingRepository extends BaseRepository implements TrainingRepositoryInterface
{
    protected string $table = 'hr_training_programs';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getEnrollmentCount(int $programId): int
    {
        $result = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as cnt FROM hr_training_enrollments WHERE training_program_id = ? AND status IN ('enrolled', 'completed')",
            [$programId]
        );

        return (int) $result['cnt'];
    }

    public function enrollEmployee(int $programId, int $employeeId): void
    {
        $this->db->connection()->insert(
            "INSERT INTO hr_training_enrollments (training_program_id, employee_id, status, enrolled_at) VALUES (?, ?, 'enrolled', ?)",
            [$programId, $employeeId, date('Y-m-d H:i:s')]
        );
    }
}