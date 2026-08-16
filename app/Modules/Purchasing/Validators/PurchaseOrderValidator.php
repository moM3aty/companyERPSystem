<?php
// Path: app/Domain/Exceptions/DomainValidationException.php

declare(strict_types=1);

namespace App\Domain\Exceptions;

/**
 * Enterprise Domain Exception: Domain Validation Exception
 * يُرمى عندما تفشل البيانات في اجتياز قواعد العمل (Business Rules) داخل الـ Validators في طبقة הـ Domain.
 * يحول مساره تلقائياً إلى 422 Unprocessable Entity في الـ API.
 */
class DomainValidationException extends DomainException
{
    public function __construct(string $message = "The provided data violates domain business rules.")
    {
        parent::__construct($message, 422);
    }
}