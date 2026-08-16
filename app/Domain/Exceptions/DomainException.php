<?php
// Path: app/Domain/Exceptions/DomainException.php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

/**
 * Enterprise Domain Exception
 * الاستثناء الأساسي لأي خطأ يقع داخل طبقة الـ Domain (معزول تماماً عن الـ Framework).
 */
class DomainException extends Exception
{
}