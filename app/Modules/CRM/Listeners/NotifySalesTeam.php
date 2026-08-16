<?php
// Path: app/Modules/CRM/Listeners/NotifySalesTeam.php

declare(strict_types=1);

namespace App\Modules\CRM\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Notifications\NotificationManager;
use App\Modules\CRM\Events\OpportunityWon;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Listener: Notify Sales Team
 * يستمع لحدث (OpportunityWon) ويقوم بإرسال تنبيه للمدير بأن المندوب الفلاني حقق البيعة.
 */
class NotifySalesTeam implements EventListener
{
    protected NotificationManager $notifier;
    protected DatabaseManager $db;

    public function __construct(NotificationManager $notifier, DatabaseManager $db)
    {
        $this->notifier = $notifier;
        $this->db = $db;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof OpportunityWon) {
            return;
        }

        // جلب مدير المبيعات لإرسال الإشعار له
        $manager = $this->db->connection()->selectOne(
            "SELECT u.id FROM users u 
             JOIN user_roles ur ON u.id = ur.user_id
             JOIN roles r ON ur.role_id = r.id
             WHERE u.company_id = ? AND r.name = 'Sales Manager' LIMIT 1",
            [$event->companyId]
        );

        if ($manager) {
            $this->notifier->send(
                (int) $manager['id'],
                'opportunity_won',
                [
                    'opportunity_id' => $event->entityId,
                    'revenue'        => $event->revenue,
                    'sales_rep_id'   => $event->assignedTo
                ],
                ['in_app', 'email']
            );
        }
    }
}