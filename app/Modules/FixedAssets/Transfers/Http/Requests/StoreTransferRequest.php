<?php
// Path: app/Modules/FixedAssets/Transfers/Http/Requests/StoreTransferRequest.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Transfers\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreTransferRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'asset_id'       => [new Required(), new Exists($this->db, 'fixed_assets', 'id', $companyId)],
            'to_branch_id'   => [new Required(), new Exists($this->db, 'branches', 'id', $companyId)],
            'to_location_id' => [new Exists($this->db, 'locations', 'id', $companyId)],
            'transfer_date'  => [new Required(), new Date('Y-m-d')],
            'notes'          => ['string'],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}