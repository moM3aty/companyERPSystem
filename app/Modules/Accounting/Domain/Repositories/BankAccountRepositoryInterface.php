<?php
// Path: app/Modules/Accounting/Domain/Repositories/BankAccountRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Repositories;

interface BankAccountRepositoryInterface
{
    public function getAll(int $companyId): array;
    
    public function findById(int $id, int $companyId): ?array;
    
    public function create(array $data, int $companyId): int;
    
    public function update(int $id, array $data, int $companyId): bool;
    
    /**
     * تحديث رصيد البنك مباشرة بناءً على العمليات
     */
    public function updateBalance(int $id, float $amount, string $operationType, int $companyId): bool;
}