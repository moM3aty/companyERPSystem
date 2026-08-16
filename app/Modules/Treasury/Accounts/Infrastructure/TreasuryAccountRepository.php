<?php
// Path: app/Modules/Treasury/Accounts/Infrastructure/TreasuryAccountRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Accounts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Treasury\Accounts\Domain\TreasuryAccount;
use App\Modules\Treasury\Accounts\Domain\TreasuryAccountRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Treasury Account
 */
class TreasuryAccountRepository extends BaseRepository implements TreasuryAccountRepositoryInterface
{
    protected string $table = 'treasury_accounts';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial accounts are deactivated, not deleted.

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByGlAccount(int $glAccountId, int $companyId): ?TreasuryAccount
    {
        $data = $this->newQuery()
                     ->where('gl_account_id', '=', $glAccountId)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new TreasuryAccount($data) : null;
    }
}