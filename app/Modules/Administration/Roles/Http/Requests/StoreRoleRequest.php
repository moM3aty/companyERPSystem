<?php
// Path: app/Modules/Administration/Roles/Http/Requests/StoreRoleRequest.php

declare(strict_types=1);

namespace App\Modules\Administration\Roles\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Max;

/**
 * Enterprise Request Validation: Store Role
 * يحمي النظام من محاولات إنشاء أدوار بأسماء فارغة أو بيانات مشوهة.
 */
class StoreRoleRequest
{
    /**
     * التحقق من البيانات.
     *
     * @param array $data
     * @return array
     * @throws \App\Core\Validation\ValidationException
     */
    public function validate(array $data): array
    {
        $rules = [
            'name'           => [new Required(), new StringRule(), new Max(50)],
            'description'    => [new StringRule(), new Max(255)],
            'permission_ids' => ['array'],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}