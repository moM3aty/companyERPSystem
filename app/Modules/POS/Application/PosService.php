<?php
// Path: app/Modules/POS/Application/PosService.php

declare(strict_types=1);

namespace App\Modules\POS\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Modules\POS\Orders\Domain\PosOrderRepositoryInterface;
use App\Modules\POS\Shifts\Domain\PosShiftRepositoryInterface;
use App\Modules\POS\Orders\Domain\Events\PosOrderCompletedEvent;

/**
 * Enterprise Application Service: POS Engine
 * العقل المدبر لنقاط البيع. يضمن عدم البيع بدون وردية مفتوحة، ويحسب المبالغ بدقة السيرفر.
 */
class PosService
{
    protected PosOrderRepositoryInterface $orderRepo;
    protected PosShiftRepositoryInterface $shiftRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        PosOrderRepositoryInterface $orderRepo,
        PosShiftRepositoryInterface $shiftRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->orderRepo = $orderRepo;
        $this->shiftRepo = $shiftRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    /**
     * إنشاء فاتورة مبيعات سريعة (POS Order).
     *
     * @param array $data
     * @param int $companyId
     * @param int $userId
     * @return int
     * @throws BusinessException|\Throwable
     */
    public function createOrder(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            // 1. التحقق من وجود وردية مفتوحة للكاشير الحالي
            $activeShift = $this->shiftRepo->getActiveShiftForUser($userId, $companyId);
            
            if (!$activeShift) {
                throw new BusinessException("You must open a POS shift before creating an order.", 403);
            }

            $shiftId = (int) $activeShift['id'];

            // 2. الحساب الرياضي على السيرفر (Server-side Math Validation)
            $subtotal = 0.0;
            $totalDiscount = 0.0;
            $totalTax = 0.0;
            $processedItems = [];

            foreach ($data['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $discount = (float) $item['discount_amount'];
                
                // في نظام كامل يتم جلب الضريبة من الصنف، هنا للتبسيط نفترض 0 أو تمرر من الـ UI
                $tax = 0.0; 

                $lineTotal = ($qty * $price) - $discount + $tax;

                $subtotal += ($qty * $price);
                $totalDiscount += $discount;
                $totalTax += $tax;

                $processedItems[] = [
                    'product_id'      => (int) $item['product_id'],
                    'quantity'        => $qty,
                    'unit_price'      => $price,
                    'tax_amount'      => $tax,
                    'discount_amount' => $discount,
                    'total'           => round($lineTotal, 2)
                ];
            }

            $grandTotal = round($subtotal - $totalDiscount + $totalTax, 2);

            // 3. حفظ بيانات الترويسة
            $orderToInsert = [
                'company_id'     => $companyId,
                'shift_id'       => $shiftId,
                'customer_id'    => $data['customer_id'] ?? null,
                'order_number'   => $this->orderRepo->generateOrderNumber($companyId),
                'subtotal'       => $subtotal,
                'tax_total'      => $totalTax,
                'discount_total' => $totalDiscount,
                'grand_total'    => $grandTotal,
                'payment_method' => $data['payment_method'],
                'status'         => 'completed', // POS is immediate
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $orderId = $this->orderRepo->create($orderToInsert);

            // 4. حفظ الأصناف
            $this->orderRepo->bulkInsertItems($orderId, $processedItems);

            // 5. إطلاق حدث البيع ليقوم الـ Inventory و הـ Accounting بالتصرف
            $this->eventBus->publish(new PosOrderCompletedEvent($orderId, $companyId, $shiftId, $grandTotal, $data['payment_method']));

            return $orderId;
        });
    }
}