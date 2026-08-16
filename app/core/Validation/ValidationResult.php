<?php
// Path: app/Core/Validation/ValidationResult.php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Enterprise Validation Result
 * كائن يحمل نتيجة عملية التحقق (البيانات الموثقة، والأخطاء إن وجدت).
 */
class ValidationResult
{
    /**
     * الأخطاء المكتشفة أثناء التحقق.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * البيانات التي تم التحقق من صحتها وتنقيتها.
     *
     * @var array
     */
    protected array $validatedData = [];


    /**
     * إضافة خطأ جديد لحقل معين.
     *
     * @param string $field
     * @param string $message
     * @return void
     */
    public function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    /**
     * التحقق مما إذا كانت هناك أي أخطاء.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * التحقق مما إذا كانت جميع البيانات صحيحة.
     *
     * @return bool
     */
    public function passes(): bool
    {
        return !$this->fails();
    }


    /**
     * الحصول على مصفوفة الأخطاء بالكامل.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * إضافة حقل تم التحقق منه بنجاح إلى البيانات الموثقة.
     *
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function addValidatedData(string $field, mixed $value): void
    {
        $this->validatedData[$field] = $value;
    }

    /**
     * الحصول على البيانات التي نجحت في التحقق فقط.
     * مفيد جداً لمنع الـ Mass Assignment Vulnerabilities.
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        return $this->validatedData;
    }
}