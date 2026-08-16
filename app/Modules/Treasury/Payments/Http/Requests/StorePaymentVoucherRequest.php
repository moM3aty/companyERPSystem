<?php
// Path: app/Modules/Treasury/Payments/Http/Requests/StorePaymentVoucherRequest.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Min;
use App\Core\Database\DatabaseManager;
use App\Core\Validation\Rules\Exists;

/**
 * Enterprise Request Validation: Store Payment Voucher
 * يضمن صحة إدخالات المحاسب قبل تمريرها לـ App Service، لمنع خروج أموال بطريقة غير مسجلة.
 */
class StorePaymentVoucherRequest
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
            'debit_account_id'    => [new Required(), new Exists($this->db, 'chart_of_accounts', 'id', $companyId)],
            'voucher_date'        => [new Required(), new Date('Y-m-d')],
            'amount'              => [new Required(), new Numeric(), new Min(0.01)],
            'currency_id'         => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'reference'           => [new StringRule()],
            'description'         => [new Required(), new StringRule()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}