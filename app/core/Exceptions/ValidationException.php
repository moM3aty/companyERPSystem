<?php
// Path: app/Core/Exceptions/ValidationException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Validation Exception
 * يُرمى عند فشل الـ Validator في التحقق من البيانات المرسلة.
 */
class ValidationException extends CoreException
{
    /**
     * @var array
     */
    protected array $errors;

    /**
     * ValidationException constructor.
     *
     * @param array $errors مصفوفة الأخطاء
     * @param string $message رسالة الخطأ
     * @param int $code كود الحالة
     * @param Throwable|null $previous
     */
    public function __construct(
        array $errors, 
        string $message = 'The given data was invalid.', 
        int $code = 422, 
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * الحصول على جميع الأخطاء.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}