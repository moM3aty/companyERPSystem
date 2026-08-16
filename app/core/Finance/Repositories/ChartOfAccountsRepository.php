<?php
// Path: app/Core/Finance/Repositories/ChartOfAccountsRepository.php

declare(strict_types=1);

namespace App\Core\Finance\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Chart of Accounts Repository
 * Manages the financial tree. Ensures strict compliance with accounting rules.
 */
class ChartOfAccountsRepository extends BaseRepository
{
    protected string $table = 'chart_of_accounts';
    protected bool $useTenantScope = true;

    /**
     * ChartOfAccountsRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Find an account by its unique code within the company.
     *
     * @param string $accountCode
     * @param int $companyId
     * @return array|null
     */
    public function findByCode(string $accountCode, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('account_code', '=', $accountCode)
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result ?: null;
    }

    /**
     * Get a valid posting account.
     * Enforces the rule that transactions CANNOT be posted to Control/Parent accounts or inactive accounts.
     *
     * @param int $accountId
     * @param int $companyId
     * @return array
     * @throws BusinessException
     */
    public function getValidPostingAccount(int $accountId, int $companyId): array
    {
        $account = $this->newQuery()
                        ->where('id', '=', $accountId)
                        ->where('company_id', '=', $companyId)
                        ->first();

        if (!$account) {
            throw new BusinessException("Account ID [{$accountId}] not found.", 404);
        }

        if ((int) $account['is_active'] !== 1) {
            throw new BusinessException("Cannot post to an inactive account: [{$account['account_name']}].", 422);
        }

        if ((int) $account['is_control_account'] === 1) {
            throw new BusinessException("Cannot post directly to a Control/Parent account: [{$account['account_name']}]. Transactions must be posted to leaf accounts.", 422);
        }

        return $account;
    }
}