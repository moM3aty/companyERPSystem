<?php
// Path: app/Modules/FixedAssets/Assets/Http/Requests/StoreAssetRequest.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Asset
 * حماية النظام من إدخال أصول ببيانات محاسبية ناقصة.
 */
class StoreAssetRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'asset_code'                          => [new Required(), new StringRule(), new Unique($this->db, 'fixed_assets', 'asset_code', null, $companyId)],
            'name'                                => [new Required(), new StringRule()],
            'purchase_date'                       => [new Required(), new Date('Y-m-d')],
            'purchase_value'                      => [new Required(), new Numeric(), new Min(0)],
            'salvage_value'                       => [new Numeric(), new Min(0)],
            'useful_life_months'                  => [new Required(), new Numeric(), new Min(1)], // العمر الافتراضي بالأشهر
            'asset_account_id'                    => [new Required(), new Exists($this->db, 'chart_of_accounts', 'id', $companyId)],
            'accumulated_depreciation_account_id' => [new Required(), new Exists($this->db, 'chart_of_accounts', 'id', $companyId)],
            'depreciation_expense_account_id'     => [new Required(), new Exists($this->db, 'chart_of_accounts', 'id', $companyId)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}