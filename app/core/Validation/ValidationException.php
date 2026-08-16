<?php
// Path: app/Core/Validation/ValidationException.php

declare(strict_types=1);

namespace App\Core\Validation;

use Exception;
use Throwable;

/**
 * Enterprise Validation Exception
 * يتم رمي هذا الاستثناء عندما تفشل عملية التحقق من صحة البيانات.
 * مصمم ليتم التقاطه بواسطة الـ Kernel لتحويله إلى JSON Response أو Redirect تلقائياً.
 */
class ValidationException extends Exception
{
    /**
     * مصفوفة تحتوي على رسائل الأخطاء لكل حقل.
     * 
     * @var array
     */
    protected array $errors;


    /**
     * ValidationException constructor.
     *
     * @param array $errors مصفوفة الأخطاء
     * @param string $message رسالة الخطأ العامة
     * @param int $code كود الحالة (غالباً 422 Unprocessable Entity)
     * @param Throwable|null $previous
     */
    public function __construct(array $errors, string $message = 'The given data was invalid.', int $code = 422, ?Throwable $previous = null)
    {
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

    /**
     * الحصول على الخطأ الأول لحقل معين (إن وجد).
     *
     * @param string $field
     * @return string|null
     */
    public function getFirstError(string $field): ?string
    {
        if (isset($this->errors[$field]) && is_array($this->errors[$field])) {
            return $this->errors[$field][0] ?? null;
        }

        return $this->errors[$field] ?? null;
    }
}