<?php
// Path: app/Modules/Payroll/Repositories/SalaryStructureRepository.php

declare(strict_types=1);

namespace App\Modules\Payroll\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Infrastructure Repository: Salary Structure
 */
class SalaryStructureRepository extends BaseRepository
{
    protected string $table = 'payroll_salary_structures';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * حفظ الهيكل الأساسي مع مكوناته (البدلات والاستقطاعات) دفعة واحدة.
     *
     * @param array $data
     * @param array $components
     * @return int
     */
    public function saveWithComponents(array $data, array $components): int
    {
        $structureId = $this->create($data);

        if (!empty($components)) {
            $values = [];
            $bindings = [];
            $placeholders = "(?, ?, ?, ?, ?)";

            foreach ($components as $comp) {
                $values[] = $placeholders;
                array_push(
                    $bindings, 
                    $structureId, 
                    $comp['type'], 
                    $comp['component_id'], 
                    $comp['amount'], 
                    $comp['is_percentage'] ?? 0
                );
            }

            $sql = "INSERT INTO payroll_salary_components 
                    (structure_id, type, component_id, amount, is_percentage) 
                    VALUES " . implode(', ', $values);
                    
            $this->db->connection()->insert($sql, $bindings);
        }

        return $structureId;
    }
}