<?php
// Path: app/Modules/HR/Jobs/CheckExpiringContractsJob.php

declare(strict_types=1);

namespace App\Modules\HR\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;
use App\Core\Notifications\NotificationManager;

/**
 * Enterprise Job: Check Expiring Contracts
 * يُجدول للعمل يومياً. يبحث عن العقود التي تنتهي خلال 30 يوماً وينبه الـ HR.
 */
class CheckExpiringContractsJob extends Job
{
    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        
        /** @var NotificationManager $notifier */
        $notifier = $container->make(NotificationManager::class);

        $thresholdDate = date('Y-m-d', strtotime('+30 days'));
        $today = date('Y-m-d');

        // جلب العقود النشطة التي تنتهي قريباً
        $sql = "SELECT c.id, c.company_id, c.end_date, e.first_name, e.last_name 
                FROM hr_contracts c
                JOIN hr_employees e ON c.employee_id = e.id
                WHERE c.status = 'active' AND c.end_date IS NOT NULL 
                  AND c.end_date BETWEEN ? AND ?";

        $contracts = $db->connection()->select($sql, [$today, $thresholdDate]);

        if (empty($contracts)) {
            return;
        }

        // تجميع التنبيهات وإرسالها لمدير الموارد البشرية لكل شركة
        foreach ($contracts as $contract) {
            $companyId = $contract['company_id'];
            
            $hrManagers = $db->connection()->select(
                "SELECT u.id FROM users u 
                 JOIN user_roles ur ON u.id = ur.user_id
                 JOIN roles r ON ur.role_id = r.id
                 WHERE u.company_id = ? AND r.name = 'HR Manager' AND u.is_active = 1",
                [$companyId]
            );

            foreach ($hrManagers as $manager) {
                $notifier->send(
                    (int) $manager['id'],
                    'contract_expiring',
                    [
                        'employee_name' => $contract['first_name'] . ' ' . $contract['last_name'],
                        'end_date'      => $contract['end_date']
                    ],
                    ['in_app', 'email']
                );
            }
        }
    }
}