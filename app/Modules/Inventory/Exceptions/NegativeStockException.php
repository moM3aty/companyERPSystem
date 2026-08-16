<?php
// Path: app/Modules/Inventory/Exceptions/NegativeStockException.php

declare(strict_types=1);

namespace App\Modules\Inventory\Exceptions;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Exception: Negative Stock
 * يضمن عدم مرور أي حركة مخزنية تؤدي لرصيد سالب إذا كانت إعدادات الشركة تمنع ذلك.
 */
class NegativeStockException extends BusinessException
{
    public function __construct(int $productId, int $warehouseId, float $requested, float $available)
    {
        $message = "Inventory Rule Violation: Insufficient stock for Product ID [{$productId}] in Warehouse ID [{$warehouseId}]. " .
                   "Requested: {$requested}, Available: {$available}. Negative stock is disabled.";
                   
        parent::__construct($message, 422);
    }
}