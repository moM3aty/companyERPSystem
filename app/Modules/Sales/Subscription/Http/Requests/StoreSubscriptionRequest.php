<?php
// Path: app/Modules/Sales/Subscription/Http/Requests/StoreSubscriptionRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreSubscriptionRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'customer_id'       => [new Required(), new Exists($this->db, 'customers', 'id', $companyId)],
            'product_id'        => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
            'billing_cycle'     => [new Required(), new In(['monthly', 'quarterly', 'yearly'])],
            'price'             => [new Required(), new Numeric(), new Min(1)],
            'currency_id'       => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'next_billing_date' => [new Required(), new Date('Y-m-d')],
            'end_date'          => [new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}