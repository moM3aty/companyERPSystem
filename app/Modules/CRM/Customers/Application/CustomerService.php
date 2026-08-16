<?php
// Path: app/Modules/CRM/Customers/Application/CustomerService.php

declare(strict_types=1);

namespace App\Modules\CRM\Customers\Application;

use App\Core\CRM\CustomerRepository;
use App\Modules\CRM\Customers\Domain\Customer;
use App\Core\Exceptions\BusinessException;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Customer
 * يضمن إنشاء العملاء وجهات اتصالهم كوحدة واحدة (Atomic) لتجنب وجود بيانات معزولة.
 */
class CustomerService
{
    protected CustomerRepository $customerRepo;
    protected TransactionManager $transaction;

    public function __construct(CustomerRepository $customerRepo, TransactionManager $transaction)
    {
        $this->customerRepo = $customerRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء عميل جديد وجهات الاتصال الخاصة به.
     *
     * @param array $customerData
     * @param array $contactsData
     * @param int $companyId
     * @return Customer
     * @throws BusinessException|\Throwable
     */
    public function createCustomer(array $customerData, array $contactsData, int $companyId): Customer
    {
        $customerData['company_id'] = $companyId;
        $customerData['is_active'] = $customerData['is_active'] ?? 1;

        // مراجعة أمنية إضافية لعدم تكرار الكود
        $this->customerRepo->setTenantId($companyId);
        if ($this->customerRepo->findByCode($customerData['customer_code'])) {
             throw new BusinessException("Customer Code '{$customerData['customer_code']}' already exists.");
        }

        $customerId = $this->customerRepo->createWithContacts($customerData, $contactsData);

        $customerArray = $this->customerRepo->findOrFail($customerId);

        return new Customer($customerArray);
    }
}