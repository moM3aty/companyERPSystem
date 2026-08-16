<?php
// Path: app/Modules/Administration/Users/Http/Requests/UpdateUserRequest.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Http\Requests;

use App\Core\Database\DatabaseManager;
use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Boolean;

/**
 * Enterprise Request Validation: Update User
 */
class UpdateUserRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $userId, int $companyId): array
    {
        $rules = [
            'username'  => [new StringRule(), new Min(3), new Max(100)],
            'email'     => [
                new Email(), 
                new Unique($this->db, 'users', 'email', $userId, $companyId) // استثناء المستخدم الحالي من الفحص
            ],
            'language'  => [new StringRule()],
            'timezone'  => [new StringRule()],
            'is_active' => [new Boolean()],
            'role_ids'  => ['array'],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}