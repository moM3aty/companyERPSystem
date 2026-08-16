<?php
// Path: app/Core/Exceptions/StorageException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Exception;
use Throwable;

/**
 * Enterprise Storage Exception
 * يتم رميه عند فشل رفع أو قراءة الملفات من السيرفر لأسباب أمنية أو تقنية.
 */
class StorageException extends Exception
{
    /**
     * StorageException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}