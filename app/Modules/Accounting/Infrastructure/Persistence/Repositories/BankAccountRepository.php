<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Repositories/BankAccountRepository.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Repositories;

use App\Modules\Accounting\Domain\Repositories\BankAccountRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Persistence\Models\BankAccountModel;

class BankAccountRepository implements BankAccountRepositoryInterface
{
    private BankAccountModel $model;

    public function __construct()
    {
        $this->model = new BankAccountModel();
    }

    public function getAll(int $companyId): array
    {
        return $this->model->fetchAll($companyId);
    }

    public function updateBalance(int $id, float $amount, string $operationType, int $companyId): bool
    {
        $actualAmount = $operationType === 'withdraw' ? -$amount : $amount;
        return $this->model->updateBalance($id, $actualAmount, $companyId);
    }

    public function findById(int $id, int $companyId): ?array { return null; }
    public function create(array $data, int $companyId): int { return 0; }
    public function update(int $id, array $data, int $companyId): bool { return false; }
}