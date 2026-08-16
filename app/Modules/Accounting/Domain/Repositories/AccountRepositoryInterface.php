<?php
// Path: app/Modules/Accounting/Domain/Repositories/AccountRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Repositories;

interface AccountRepositoryInterface
{
    public function getAll(int $companyId): array;
    
    public function findById(int $id, int $companyId): ?array;
    
    public function findByCode(string $code, int $companyId): ?array;
    
    public function create(array $data, int $companyId): int;
    
    public function update(int $id, array $data, int $companyId): bool;
    
    public function delete(int $id, int $companyId): bool;
    
    /**
     * حساب رصيد الحساب المباشر من خلال القيود المرحّلة فقط
     */
    public function calculateBalance(int $accountId, int $companyId): float;
}