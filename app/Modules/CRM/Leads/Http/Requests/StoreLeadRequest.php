<?php
// Path: app/Modules/CRM/Leads/Http/Requests/StoreLeadRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreLeadRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'first_name'   => [new Required(), new StringRule()],
            'last_name'    => [new StringRule()],
            'company_name' => [new StringRule()],
            'email'        => [new Email()],
            'phone'        => [new Required(), new StringRule()],
            'source'       => [new StringRule()],
            'assigned_to'  => [new Exists($this->db, 'users', 'id', $companyId)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}