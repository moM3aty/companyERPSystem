<?php
// Path: app/Modules/Purchasing/Suppliers/Http/Requests/StoreSupplierRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Supplier
 * فلترة مدخلات المورد ومنع تكرار كود المورد في نفس الشركة.
 */
class StoreSupplierRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'supplier_code'   => [new Required(), new StringRule(), new Max(50), new Unique($this->db, 'suppliers', 'supplier_code', null, $companyId)],
            'name'            => [new Required(), new StringRule(), new Max(255)],
            'email'           => [new Email(), new Max(150)],
            'phone'           => [new StringRule(), new Max(50)],
            'tax_number'      => [new StringRule(), new Max(100)],
            'credit_limit'    => [new Numeric(), new Min(0)],
            'payment_term_id' => [new Numeric()], // Ideally use Exists rule here
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}