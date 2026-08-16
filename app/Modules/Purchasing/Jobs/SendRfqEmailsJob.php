<?php
// Path: app/Modules/Purchasing/Jobs/SendRfqEmailsJob.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;
use App\Core\Notifications\NotificationManager;

/**
 * Enterprise Job: Send RFQ Emails
 * يعمل في الخلفية (Background) بعد إنشاء طلب التسعير (RFQ) لإرسال إيميلات رسمية 
 * لكل الموردين المدعوين مع رابط لتقديم العرض.
 */
class SendRfqEmailsJob extends Job
{
    public readonly int $rfqId;
    public readonly int $companyId;

    public function __construct(int $rfqId, int $companyId)
    {
        $this->rfqId = $rfqId;
        $this->companyId = $companyId;
        $this->retryPolicy = new \App\Core\Queue\RetryPolicy(3, 120); // 3 محاولات، الفاصل دقيقتين
    }

    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        
        /** @var NotificationManager $notifier */
        $notifier = $container->make(NotificationManager::class);

        $rfq = $db->connection()->selectOne("SELECT rfq_number, title, deadline_date FROM purchasing_rfqs WHERE id = ?", [$this->rfqId]);
        
        if (!$rfq) return;

        // جلب إيميلات الموردين المدعوين
        $suppliers = $db->connection()->select(
            "SELECT s.id, s.name, s.email 
             FROM purchasing_rfq_suppliers rs 
             JOIN suppliers s ON rs.supplier_id = s.id 
             WHERE rs.rfq_id = ? AND s.email IS NOT NULL",
            [$this->rfqId]
        );

        foreach ($suppliers as $supplier) {
            $data = [
                'supplier_name' => $supplier['name'],
                'rfq_number'    => $rfq['rfq_number'],
                'rfq_title'     => $rfq['title'],
                'deadline'      => $rfq['deadline_date'],
                // الرابط السري للمورد للدخول على بوابة الموردين وإدخال الأسعار
                'portal_link'   => "https://erp.com/portal/rfq/{$this->rfqId}/bid?token=" . bin2hex(random_bytes(16))
            ];

            // نرسل الإشعار عبر قناة الإيميل فقط (لا نرسل in_app لأنهم موردين خارجيين)
            $notifier->send((int) $supplier['id'], 'rfq_invitation', $data, ['email']);
        }
    }
}