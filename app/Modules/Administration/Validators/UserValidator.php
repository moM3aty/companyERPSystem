<?php
// Path: app/Modules/Administration/Validators/UserValidator.php

declare(strict_types=1);

namespace App\Modules\Administration\Validators;

use App\Domain\Exceptions\DomainValidationException;

/**
 * Enterprise Domain Validator: User
 * يفصل قواعد الأعمال الصارمة (Business Logic Validation) عن قواعد الإدخال (HTTP Form Validation).
 */
class UserValidator
{
    /**
     * فحص منطق الأعمال الداخلي قبل الحفظ في قاعدة البيانات.
     *
     * @param array $domainData
     * @return void
     * @throws DomainValidationException
     */
    public function validateForDomain(array $domainData): void
    {
        // مثال: منع تسمية المستخدم بـ "admin" إذا كان مخصصاً للنظام الأساسي فقط
        $restrictedUsernames = ['admin', 'root', 'system', 'supervisor'];

        if (isset($domainData['username']) && in_array(strtolower($domainData['username']), $restrictedUsernames, true)) {
            // (يجب أن نكون قد أنشأنا هذا الاستثناء في الـ Core/Domain، سنفترض وجوده)
            throw new \Exception("Business Rule Violation: The username '{$domainData['username']}' is reserved for system use.");
        }
    }
}