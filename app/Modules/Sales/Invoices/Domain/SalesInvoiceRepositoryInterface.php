<?php
// Path: app/Modules/Sales/Invoices/Domain/SalesInvoiceRepositoryInterface.php

namespace App\Modules\Sales\Invoices\Domain;

interface SalesInvoiceRepositoryInterface
{
    /**
     * Find a sales invoice by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Get all sales invoices.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Create a new sales invoice.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update an existing sales invoice.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;
}