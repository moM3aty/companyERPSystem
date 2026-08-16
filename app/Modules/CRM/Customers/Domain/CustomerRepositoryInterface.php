<?php
// Path: app/Modules/CRM/Customers/Domain/CustomerRepositoryInterface.php

namespace App\Modules\CRM\Customers\Domain;

interface CustomerRepositoryInterface
{
    /**
     * Find a customer by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Get all customers.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Create a new customer.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update an existing customer.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a customer.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}