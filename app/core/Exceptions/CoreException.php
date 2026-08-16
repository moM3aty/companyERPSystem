<?php
// Path: app/Core/Exceptions/CoreException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Exception;
use Throwable;

/**
 * Enterprise Core Exception
 * الكلاس الأساسي لكل الاستثناءات داخل النظام. 
 * يسمح بتمرير بيانات إضافية (Context) لتسهيل تتبع الأخطاء في الـ Logs.
 */
abstract class CoreException extends Exception
{
    /**
     * @var array
     */
    protected array $context = [];

    /**
     * CoreException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(string $message = "", int $code = 500, ?Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return self
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }
}