<?php
// Path: app/Modules/Manufacturing/Routings/Infrastructure/RoutingRepository.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routings\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class RoutingRepository extends BaseRepository
{
    protected string $table = 'manufacturing_routings';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * إدخال خطوات مسار التصنيع دفعة واحدة بأداء عالي.
     */
    public function bulkInsertSteps(int $routingId, array $steps): void
    {
        if (empty($steps)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?)";

        foreach ($steps as $step) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $routingId,
                $step['work_center_id'],
                $step['step_number'],
                $step['operation_name'],
                $step['setup_time_minutes'] ?? 0.0,
                $step['execution_time_minutes'] ?? 0.0
            );
        }

        $sql = "INSERT INTO manufacturing_routing_steps 
                (routing_id, work_center_id, step_number, operation_name, setup_time_minutes, execution_time_minutes) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}