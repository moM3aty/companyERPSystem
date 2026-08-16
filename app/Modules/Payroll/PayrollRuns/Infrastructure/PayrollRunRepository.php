<?php
// Path: app/Modules/Payroll/PayrollRuns/Infrastructure/PayrollRunRepository.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Payroll\PayrollRuns\Domain\PayrollRunRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Payroll Run
 */
class PayrollRunRepository extends BaseRepository implements PayrollRunRepositoryInterface
{
    protected string $table = 'payroll_runs';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial records are strictly non-deletable

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function existsForPeriod(string $runPeriod, int $companyId): bool
    {
        $result = $this->newQuery()
                       ->where('run_period', '=', $runPeriod)
                       ->where('company_id', '=', $companyId)
                       ->where('status', '!=', 'cancelled')
                       ->first();

        return $result !== null;
    }
}