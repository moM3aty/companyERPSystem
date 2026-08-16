<?php
// Path: app/Modules/Inventory/StockMovements/Domain/StockMovementType.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Domain;

/**
 * Enterprise Enum: Stock Movement Type
 */
enum StockMovementType: string
{
    case IN = 'IN';                 // وارد (شراء، استرجاع عميل)
    case OUT = 'OUT';               // منصرف (بيع، إتلاف)
    case TRANSFER = 'TRANSFER';     // نقل بين المستودعات
    case ADJUSTMENT = 'ADJUSTMENT'; // تسوية جردية
}