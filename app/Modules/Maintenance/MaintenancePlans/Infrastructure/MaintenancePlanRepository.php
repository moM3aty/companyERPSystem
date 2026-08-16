<?php
// Path: app/Modules/Maintenance/MaintenancePlans/Infrastructure/MaintenancePlanRepository.php

declare(strict_types=1);

namespace App\Modules\Maintenance\MaintenancePlans\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Maintenance\MaintenancePlans\Domain\MaintenancePlanRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Maintenance Plan
 */
class MaintenancePlanRepository extends BaseRepository implements MaintenancePlanRepositoryInterface
{
    protected string $table = 'maintenance_plans';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    
    /**
     * @inheritDoc
     */
    public function getDuePlans(string $date, int $companyId): array
    {
        return $this->newQuery()
                    ->where('next_due_date', '<=', $date)
                    ->where('status', '=', 'active')
                    ->where('company_id', '=', $companyId)
                    ->get();
    }
}