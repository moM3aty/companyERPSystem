<?php
// Path: app/Modules/Administration/Users/Http/Requests/StoreUserRequest.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Http\Requests;

use App\Core\Database\DatabaseManager;
use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;

/**
 * Enterprise Request Validation: Store User
 * طبقة الحماية (Validation) للتحقق من البيانات القادمة من الـ API قبل وصولها للـ Service.
 */
class StoreUserRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق من البيانات وتنقيتها.
     *
     * @param array $data
     * @param int $companyId
     * @return array البيانات الموثقة
     * @throws \App\Core\Validation\ValidationException
     */
    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'username' => [new Required(), new StringRule(), new Min(3), new Max(100)],
            'email'    => [
                new Required(), 
                new Email(), 
                new Unique($this->db, 'users', 'email', null, $companyId) // منع تكرار الإيميل داخل نفس الشركة
            ],
            'password' => [new Required(), new StringRule(), new Min(8)],
            'language' => ['string'],
            'timezone' => ['string'],
            'role_ids' => ['array'], // مصفوفة بصلاحيات المستخدم
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}