<?php
// Path: app/Modules/Payroll/Listeners/SendPayslipNotification.php

declare(strict_types=1);

namespace App\Modules\Payroll\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Core\Notifications\NotificationManager;
use App\Modules\Payroll\PayrollRuns\Domain\Events\PayrollRunProcessedEvent;

/**
 * Enterprise Listener: Send Payslip Notification
 * يُرسل إشعاراً لكل الموظفين عبر الإيميل وتطبيق الموبايل (In-App) فور ترحيل الرواتب.
 */
class SendPayslipNotification implements EventListener
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
        if (!$event instanceof PayrollRunProcessedEvent) {
            return;
        }

        // جلب الموظفين المرتبطين بهذا المسير والذين لديهم حسابات User (لإرسال الإشعارات)
        $sql = "SELECT u.id, u.email, u.fcm_token 
                FROM payroll_payslips p
                JOIN users u ON p.employee_id = u.employee_id
                WHERE p.payroll_run_id = ? AND u.is_active = 1";

        $users = $this->db->connection()->select($sql, [$event->entityId]);

        foreach ($users as $user) {
            $this->notifier->send(
                (int) $user['id'],
                'payslip_issued',
                [
                    'period' => $event->runPeriod,
                    'link'   => "/ess/payslips"
                ],
                ['in_app', 'email', 'push'] // إرسال لجميع القنوات
            );
        }
    }
}