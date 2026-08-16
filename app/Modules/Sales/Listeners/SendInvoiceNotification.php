<?php
// Path: app/Modules/Sales/Listeners/SendInvoiceNotification.php

declare(strict_types=1);

namespace App\Modules\Sales\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Core\Notifications\NotificationManager;
use App\Modules\Sales\Invoices\Domain\Events\InvoicePostedEvent;

/**
 * Enterprise Listener: Send Invoice Notification
 * يستمع لحدث اعتماد فاتورة المبيعات ويقوم بإرسالها فوراً للعميل عبر الإيميل أو الـ SMS.
 */
class SendInvoiceNotification implements EventListener
{
    protected DatabaseManager $db;
    protected NotificationManager $notifier;

    public function __construct(DatabaseManager $db, NotificationManager $notifier)
    {
        $this->db = $db;
        $this->notifier = $notifier;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof InvoicePostedEvent) {
            return;
        }

        // جلب إيميل العميل
        $customer = $this->db->connection()->selectOne(
            "SELECT email, name FROM customers WHERE id = ?",
            [$event->customerId]
        );

        if (!$customer || empty($customer['email'])) {
            return;
        }

        // جلب رقم الفاتورة
        $invoice = $this->db->connection()->selectOne(
            "SELECT invoice_no FROM sales_invoices WHERE id = ?",
            [$event->entityId]
        );

        // إرسال الإشعار عبر قناة الإيميل (بافتراض أن NotificationManager يدعم إرسال إيميلات خارجية)
        // يتم استخدام user_id افتراضي (0) لأن العميل الخارجي ليس مستخدماً في الـ users table في هذه الحالة
        // الـ NotificationManager سيحتاج لدعم الـ AdHoc Emails لاحقاً
        
        $this->notifier->send(
            0, // System/Guest indicator
            'invoice_issued',
            [
                'invoice_no'   => $invoice['invoice_no'],
                'customer_name'=> $customer['name'],
                'grand_total'  => $event->grandTotal,
            ],
            ['email'] 
        );
    }
}