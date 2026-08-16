<?php
// Path: app/Modules/Administration/Jobs/ExportUsersJob.php

declare(strict_types=1);

namespace App\Modules\Administration\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;
use App\Core\Files\FileManager;
use App\Core\Notifications\NotificationManager;

/**
 * Enterprise Background Job: Export Users
 * وظيفة تعمل في الخلفية لتصدير ملايين المستخدمين كـ CSV دون إيقاف استجابة السيرفر.
 */
class ExportUsersJob extends Job
{
    public readonly int $companyId;
    public readonly int $requestedById;

    public function __construct(int $companyId, int $requestedById)
    {
        $this->companyId = $companyId;
        $this->requestedById = $requestedById;
    }

    /**
     * التنفيذ الفعلي للوظيفة داخل הـ Queue Worker.
     *
     * @param Container $container
     * @return void
     * @throws \Exception
     */
    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        
        /** @var FileManager $files */
        $files = $container->make(FileManager::class);

        /** @var NotificationManager $notifier */
        $notifier = $container->make(NotificationManager::class);

        // 1. سحب البيانات بكفاءة
        $users = $db->connection()->select(
            "SELECT id, username, email, is_active FROM users WHERE company_id = ?",
            [$this->companyId]
        );

        if (empty($users)) {
            return;
        }

        // 2. تحويلها لـ CSV (محاكاة سريعة للكتابة في الذاكرة المؤقتة)
        $tempFile = tempnam(sys_get_temp_dir(), 'exp_');
        $handle = fopen($tempFile, 'w');
        
        fputcsv($handle, ['ID', 'Username', 'Email', 'Status']);
        foreach ($users as $user) {
            fputcsv($handle, [
                $user['id'], 
                $user['username'], 
                $user['email'], 
                $user['is_active'] ? 'Active' : 'Disabled'
            ]);
        }
        fclose($handle);

        // 3. تخزين الملف بأمان
        $fileData = [
            'name'     => "users_export_{$this->companyId}_" . time() . ".csv",
            'tmp_name' => $tempFile,
            'error'    => UPLOAD_ERR_OK,
            'size'     => filesize($tempFile)
        ];
        
        $path = $files->store($fileData, 'exports/admin', ['text/csv', 'text/plain']);
        unlink($tempFile);

        // 4. إبلاغ المدير بانتهاء التصدير ورابط الملف
        $notifier->send(
            $this->requestedById, 
            'export_completed', 
            ['report_name' => 'Users List', 'download_link' => $path],
            ['in_app', 'email']
        );
    }
}