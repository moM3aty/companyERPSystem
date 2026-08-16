<?php
// Path: app/Modules/Accounting/Domain/ValueObjects/AccountCode.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Enterprise Value Object: Account Code
 * يضمن أن كود الحساب يتبع النمط المحاسبي للمؤسسة (أرقام فقط، طول محدد).
 */
final class AccountCode
{
    private string $code;

    public function __construct(string $code)
    {
        $code = trim($code);

        // التحقق من أن الكود يحتوي على أرقام فقط (حسب شجرة الحسابات Standard)
        if (!preg_match('/^[0-9]+$/', $code)) {
            throw new InvalidArgumentException("Account code must contain only numbers. Given: {$code}");
        }

        // التحقق من طول الكود (مثال: يجب أن يكون بين 4 إلى 8 أرقام)
        if (strlen($code) < 4 || strlen($code) > 8) {
            throw new InvalidArgumentException("Account code length must be between 4 and 8 digits.");
        }

        $this->code = $code;
    }

    public function getValue(): string
    {
        return $this->code;
    }

    public function getParentCode(): string
    {
        // استخراج كود الأب (مثال: حساب 1010 الأب له هو 1000 أو 101)
        return substr($this->code, 0, -1); 
    }

    public function __toString(): string
    {
        return $this->code;
    }
}