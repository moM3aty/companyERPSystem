<?php
// Path: app/Modules/Manufacturing/Listeners/ConsumeMaterials.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Modules\Manufacturing\ProductionOrders\Domain\Events\ProductionOrderCompletedEvent;

/**
 * Enterprise Listener: Consume Materials & Receive FG
 * فور اكتمال أمر الإنتاج، يقوم هذا الكلاس بسحب المواد الخام من المخزن وإضافة المنتج النهائي.
 */
class ConsumeMaterials implements EventListener
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
        if (!$event instanceof ProductionOrderCompletedEvent) {
            return;
        }

        $orderId = $event->orderId;
        $companyId = $event->companyId;
        
        // نجلب المستودع الافتراضي للمصنع (من أمر الإنتاج)
        $order = $this->db->connection()->selectOne("SELECT order_number, branch_id FROM manufacturing_production_orders WHERE id = ?", [$orderId]);
        $warehouseId = 1; // Default fallback, should be fetched dynamically

        // 1. إضافة المنتج النهائي للمخزن (Finished Good IN)
        $this->stockService->recordMovement(
            $event->productId,
            $warehouseId,
            $event->producedQuantity,
            StockMovementType::IN,
            'production_order',
            $orderId,
            $companyId,
            0, // System
            0.0, // التكلفة ستُحسب آلياً من المواد الخام في النسخة المتقدمة
            "Produced via Order #{$order['order_number']}"
        );

        // 2. سحب المواد الخام (Raw Materials OUT)
        $items = $this->db->connection()->select(
            "SELECT component_product_id, required_quantity FROM manufacturing_production_order_items WHERE production_order_id = ?",
            [$orderId]
        );

        foreach ($items as $item) {
            $this->stockService->recordMovement(
                (int) $item['component_product_id'],
                $warehouseId,
                (float) $item['required_quantity'],
                StockMovementType::OUT,
                'production_order_consumption',
                $orderId,
                $companyId,
                0,
                0.0,
                "Consumed for Order #{$order['order_number']}"
            );
        }
    }
}