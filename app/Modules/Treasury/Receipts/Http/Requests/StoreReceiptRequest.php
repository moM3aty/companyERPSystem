<?php
// Path: app/Modules/Treasury/Receipts/Http/Requests/StoreReceiptRequest.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Min;
use App\Core\Database\DatabaseManager;
use App\Core\Validation\Rules\Exists;

/**
 * Enterprise Request Validation: Store Receipt
 * يفحص الطلب القادم من المحاسب قبل تمريره لـ App Service.
 */
class StoreReceiptRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'treasury_account_id' => [new Required(), new Exists($this->db, 'treasury_accounts', 'id', $companyId)],
            'credit_account_id'   => [new Required(), new Exists($this->db, 'chart_of_accounts', 'id', $companyId)],
            'receipt_date'        => [new Required(), new Date('Y-m-d')],
            'amount'              => [new Required(), new Numeric(), new Min(0.01)], // لا يمكن استلام مبلغ صفر أو سالب
            'currency_id'         => [new Required(), new Exists($this->db, 'currencies', 'id')], // العملات Global
            'reference'           => [new StringRule()],
            'description'         => [new Required(), new StringRule()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}