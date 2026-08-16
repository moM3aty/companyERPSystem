<?php
// Path: app/Core/CRM/CustomerRepository.php

declare(strict_types=1);

namespace App\Core\CRM;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Customer Repository
 * Manages Customer records and their associated nested data (Contacts).
 */
class CustomerRepository extends BaseRepository
{
    protected string $table = 'customers';
    protected bool $useTenantScope = true;

    protected TransactionManager $transactionManager;

    /**
     * CustomerRepository constructor.
     *
     * @param DatabaseManager $db
     * @param TransactionManager $transactionManager
     */
    public function __construct(DatabaseManager $db, TransactionManager $transactionManager)
    {
        parent::__construct($db);
        $this->transactionManager = $transactionManager;
    }

    /**
     * Find a customer by their unique customer code within the current company.
     *
     * @param string $customerCode
     * @return array|null
     */
    public function findByCode(string $customerCode): ?array
    {
        $result = $this->newQuery()
                       ->where('customer_code', '=', $customerCode)
                       ->first();

        return $result ?: null;
    }

    /**
     * Create a new customer along with their primary contacts in a single transaction.
     *
     * @param array $customerData
     * @param array $contactsData
     * @return int The ID of the newly created customer
     * @throws DatabaseException|\Throwable
     */
    public function createWithContacts(array $customerData, array $contactsData = []): int
    {
        return $this->transactionManager->execute(function () use ($customerData, $contactsData) {
            
            // 1. Create the main customer record
            $customerId = $this->create($customerData);

            // 2. Create associated contacts if provided
            if (!empty($contactsData)) {
                $contactsQuery = $this->db->connection()->prepareInsert('customer_contacts'); // Hypothetical helper or direct loop
                
                foreach ($contactsData as $contact) {
                    $contact['customer_id'] = $customerId;
                    
                    $this->db->connection()->insert(
                        "INSERT INTO customer_contacts (customer_id, name, job_title, email, phone, mobile, is_primary) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [
                            $contact['customer_id'],
                            $contact['name'],
                            $contact['job_title'] ?? null,
                            $contact['email'] ?? null,
                            $contact['phone'] ?? null,
                            $contact['mobile'] ?? null,
                            $contact['is_primary'] ?? 0
                        ]
                    );
                }
            }

            return $customerId;
        });
    }
}