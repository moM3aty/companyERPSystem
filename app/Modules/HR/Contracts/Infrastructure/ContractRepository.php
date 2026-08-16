<?php
// Path: app/Modules/HR/Contracts/Infrastructure/ContractRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Contracts\Domain\Contract;
use App\Modules\HR\Contracts\Domain\ContractRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Contract
 */
class ContractRepository extends BaseRepository implements ContractRepositoryInterface
{
    protected string $table = 'hr_contracts';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getActiveContract(int $employeeId, int $companyId): ?Contract
    {
        $data = $this->newQuery()
                     ->where('employee_id', '=', $employeeId)
                     ->where('company_id', '=', $companyId)
                     ->where('status', '=', 'active')
                     ->orderBy('id', 'desc')
                     ->first();

        return $data ? new Contract($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function deactivatePreviousContracts(int $employeeId, int $companyId): void
    {
        $this->db->connection()->update(
            "UPDATE {$this->table} SET status = 'expired', updated_at = ? 
             WHERE employee_id = ? AND company_id = ? AND status = 'active'",
            [date('Y-m-d H:i:s'), $employeeId, $companyId]
        );
    }
}