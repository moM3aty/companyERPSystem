<?php
// Path: app/Modules/FixedAssets/Disposal/Http/Requests/StoreDisposalRequest.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Disposal\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreDisposalRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'asset_id'         => [new Required(), new Exists($this->db, 'fixed_assets', 'id', $companyId)],
            'disposal_date'    => [new Required(), new Date('Y-m-d')],
            'disposal_type'    => [new Required(), new In(['sold', 'scrapped'])],
            'sale_amount'      => [new Numeric(), new Min(0)],
            'debit_account_id' => [new Exists($this->db, 'chart_of_accounts', 'id', $companyId)], // حساب الصندوق/العميل إذا تم البيع
            'reason'           => ['string'],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if ($validated['disposal_type'] === 'sold') {
            if (empty($validated['sale_amount']) || empty($validated['debit_account_id'])) {
                throw new ValidationException(['sale_amount' => ['Sale amount and Debit Account are required when disposing an asset by sale.']]);
            }
        }

        return $validated;
    }
}