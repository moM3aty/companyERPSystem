<?php
// Path: app/Modules/Inventory/Exceptions/BatchExpiredException.php

declare(strict_types=1);

namespace App\Modules\Inventory\Exceptions;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Exception: Batch Expired
 * يُرمى عند محاولة صرف كمية من تشغيلة منتهية الصلاحية (مهم للأدوية والأغذية).
 */
class BatchExpiredException extends BusinessException
{
    public function __construct(string $batchNumber, string $expiryDate)
    {
        $message = "Inventory Safety Violation: Cannot dispatch items from Batch [{$batchNumber}] because it expired on {$expiryDate}.";
                   
        parent::__construct($message, 422);
    }
}