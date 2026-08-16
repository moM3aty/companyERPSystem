<?php
// Path: app/Modules/Accounting/Domain/Repositories/TaxRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Repositories;

interface TaxRepositoryInterface
{
    public function getAllActive(int $companyId): array;
    
    public function findById(int $id, int $companyId): ?array;
    
    public function findByCode(string $code, int $companyId): ?array;
    
    public function create(array $data, int $companyId): int;
    
    public function update(int $id, array $data, int $companyId): bool;
}