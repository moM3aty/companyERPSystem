<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Repositories/AccountRepository.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Repositories;

use App\Modules\Accounting\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Persistence\Models\AccountModel;

class AccountRepository implements AccountRepositoryInterface
{
    private AccountModel $model;

    public function __construct()
    {
        $this->model = new AccountModel();
    }

    public function getAll(int $companyId): array
    {
        return $this->model->fetchAll($companyId);
    }

    public function findById(int $id, int $companyId): ?array
    {
        return $this->model->fetchById($id, $companyId);
    }

    public function findByCode(string $code, int $companyId): ?array
    {
        return $this->model->fetchByCode($code, $companyId);
    }

    public function create(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        return $this->model->insert($data);
    }

    public function update(int $id, array $data, int $companyId): bool
    {
        // For simplicity, we just update status in this demo
        return $this->model->updateStatus($id, $companyId, $data['is_active']);
    }

    public function delete(int $id, int $companyId): bool
    {
        // Soft delete mapped to updateStatus
        return $this->model->updateStatus($id, $companyId, 0);
    }

    public function calculateBalance(int $accountId, int $companyId): float
    {
        // Logic to calculate sum from JournalEntryLineModel based on account
        return 0.0;
    }
}