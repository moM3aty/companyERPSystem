<?php
// Path: app/Modules/Inventory/LandedCost/Http/Requests/StoreLandedCostRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreLandedCostRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'goods_receipt_id'    => [new Required(), new Exists($this->db, 'purchasing_goods_receipts', 'id', $companyId)],
            'purchase_invoice_id' => [new Required(), new Exists($this->db, 'purchase_invoices', 'id', $companyId)],
            'total_cost'          => [new Required(), new Numeric(), new Min(0.01)],
            'allocation_method'   => [new Required(), new In(['by_value', 'by_quantity'])],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}