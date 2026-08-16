<?php
// Path: app/Modules/Treasury/Services/BankService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Services;

use App\Modules\Treasury\Repositories\BankRepository;
use App\Modules\Treasury\Repositories\BankAccountRepository;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Bank Management
 * يدير البنوك وحساباتها.
 */
class BankService
{
    protected BankRepository $bankRepo;
    protected BankAccountRepository $accountRepo;
    protected TransactionManager $transaction;

    public function __construct(BankRepository $bankRepo, BankAccountRepository $accountRepo, TransactionManager $transaction)
    {
        $this->bankRepo = $bankRepo;
        $this->accountRepo = $accountRepo;
        $this->transaction = $transaction;
    }

    public function createBank(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->bankRepo->create($data);
    }

    public function createBankAccount(array $data, int $companyId): int
    {
        if (!empty($data['iban']) && $this->accountRepo->ibanExists($data['iban'], $companyId)) {
            throw new BusinessException("Bank Account with IBAN [{$data['iban']}] already exists.");
        }

        $data['company_id'] = $companyId;
        $data['current_balance'] = 0.00; // الرصيد يبدأ بـ 0 ويُعدل عبر قيد افتتاحي
        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->accountRepo->create($data);
    }
}