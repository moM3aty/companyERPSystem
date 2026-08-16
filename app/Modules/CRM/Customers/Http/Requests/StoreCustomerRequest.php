<?php
// Path: app/Modules/CRM/Customers/Http/Requests/StoreCustomerRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\Customers\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Unique;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store Customer
 * يضمن إدخال بيانات العميل بالإضافة لمصفوفة جهات الاتصال بشكل موثوق ومرتب.
 */
class StoreCustomerRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @param array $data
     * @param int $companyId
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'customer_code' => [new Required(), new StringRule(), new Max(50), new Unique($this->db, 'customers', 'customer_code', null, $companyId)],
            'name'          => [new Required(), new StringRule(), new Max(255)],
            'email'         => [new Email(), new Max(150)],
            'phone'         => [new StringRule(), new Max(50)],
            'tax_number'    => [new StringRule(), new Max(100)],
            'contacts'      => ['array'],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);
        
        $validated['contacts'] = $data['contacts'] ?? [];

        // التحقق من مصفوفة جهات الاتصال إن وجدت
        if (!empty($validated['contacts']) && is_array($validated['contacts'])) {
             foreach ($validated['contacts'] as $index => $contact) {
                 $contactRules = [
                     'name'  => [new Required(), new StringRule(), new Max(150)],
                     'email' => [new Email(), new Max(150)],
                     'phone' => [new StringRule(), new Max(50)],
                 ];
                 try {
                     $validated['contacts'][$index] = ValidatorFactory::makeAndValidate($contact, $contactRules);
                     $validated['contacts'][$index]['job_title'] = $contact['job_title'] ?? null;
                     $validated['contacts'][$index]['is_primary'] = $contact['is_primary'] ?? 0;
                 } catch (ValidationException $e) {
                     throw new ValidationException(["contacts.{$index}" => $e->getErrors()], "Validation failed for contact at row " . ($index + 1));
                 }
             }
        }

        return $validated;
    }
}