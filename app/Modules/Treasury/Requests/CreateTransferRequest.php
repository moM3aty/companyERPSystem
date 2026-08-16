<?php
// Path: app/Modules/Treasury/Requests/CreateTransferRequest.php

declare(strict_types=1);

namespace App\Modules\Treasury\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\StringRule;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class CreateTransferRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'from_account_id' => [new Required(), new Exists($this->db, 'treasury_accounts', 'id', $companyId)],
            'to_account_id'   => [new Required(), new Exists($this->db, 'treasury_accounts', 'id', $companyId)],
            'amount'          => [new Required(), new Numeric(), new Min(0.01)],
            'transfer_date'   => [new Required(), new Date('Y-m-d')],
            'description'     => [new Required(), new StringRule()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // لا يمكن التحويل لنفس الحساب
        if ($validated['from_account_id'] == $validated['to_account_id']) {
            throw new ValidationException(['to_account_id' => ['Source and destination accounts must be different.']]);
        }

        return $validated;
    }
}