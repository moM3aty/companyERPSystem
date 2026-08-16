<?php
// Path: app/Core/Validation/ValidatorFactory.php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Enterprise Validator Factory
 * مصنع لإنشاء كائنات الـ Validator بسهولة من الـ Controllers مع تمرير البيانات والقواعد المطلوبة.
 */
class ValidatorFactory
{
    /**
     * إنشاء وبدء التحقق الفوري.
     *
     * @param array $data البيانات القادمة من الـ Request
     * @param array $rules قواعد التحقق (مثال: ['email' => 'required|email'])
     * @return array البيانات الموثقة
     * @throws ValidationException إذا فشل التحقق
     */
    public static function makeAndValidate(array $data, array $rules): array
    {
        $validator = new Validator($data, $rules);
        return $validator->validateOrFail();
    }
}