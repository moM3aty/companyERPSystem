<?php
// Path: app/Modules/POS/Listeners/UpdateInventory.php

declare(strict_types=1);

namespace App\Modules\POS\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Modules\POS\Orders\Domain\Events\PosOrderCompletedEvent;

/**
 * Enterprise Listener: POS Update Inventory
 * بمجرد ضرب الكاشير لفاتورة، يتم خصم البضاعة من المستودع المتصل بالكاشير فوراً.
 */
class UpdateInventory implements EventListener
{
    protected DatabaseManager $db;
    protected StockService $stockService;

    public function __construct(DatabaseManager $db, StockService $stockService)
    {
        $this->db = $db;
        $this->stockService = $stockService;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof PosOrderCompletedEvent) {
            return;
        }

        $orderId = $event->entityId; // order id
        
        // جلب الفرع لخصم البضاعة من مستودعه
        $order = $this->db->connection()->selectOne("SELECT branch_id, order_number FROM pos_orders WHERE id = ?", [$orderId]);
        $warehouseId = 1; // الافتراضي إذا لم يُربط الفرع بمستودع محدد للتبسيط

        $items = $this->db->connection()->select("SELECT product_id, quantity FROM pos_order_items WHERE order_id = ?", [$orderId]);

        foreach ($items as $item) {
            $this->stockService->recordMovement(
                (int) $item['product_id'],
                $warehouseId,
                (float) $item['quantity'],
                StockMovementType::OUT,
                'pos_sale',
                $orderId,
                $event->companyId,
                0, // System execution
                0.0,
                "POS Sale #{$order['order_number']}"
            );
        }
    }
}