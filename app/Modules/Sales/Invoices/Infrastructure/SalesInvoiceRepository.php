<?php
// Path: app/Modules/Sales/Invoices/Infrastructure/SalesInvoiceRepository.php

namespace App\Modules\Sales\Invoices\Infrastructure;

use App\Modules\Sales\Invoices\Domain\SalesInvoiceRepositoryInterface;

class SalesInvoiceRepository implements SalesInvoiceRepositoryInterface
{
    
    public function findById(int $id)
    {
        // TODO: Implement findById() database logic
        return null;
    }

    public function getAll(): array
    {
        // TODO: Implement getAll() database logic
        return [];
    }

    public function create(array $data)
    {
        // TODO: Implement create() database logic
        return null;
    }

    public function update(int $id, array $data): bool
    {
        // TODO: Implement update() database logic
        return false;
    }
}