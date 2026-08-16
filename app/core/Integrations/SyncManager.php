<?php
// Path: app/Core/Integrations/SyncManager.php
declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Sync Manager
 * يدير عمليات المزامنة المجدولة بين الـ ERP والأنظمة الخارجية.
 * يتتبع متى كانت آخر مزامنة وما هي السجلات التي يجب رفعها.
 */
class SyncManager
{
    protected DatabaseManager $db;


    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تحديث وقت آخر مزامنة لربط معين.
     *
     * @param int $integrationId
     * @return void
     */
    public function markSyncComplete(int $integrationId): void
    {
        $this->db->connection()->update(
            "UPDATE integrations SET last_sync_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $integrationId]
        );
    }

    /**
     * التحقق مما إذا كان حان وقت المزامنة.
     *
     * @param Integration $integration
     * @return bool
     */
    public function isDueForSync(Integration $integration): bool
    {
        $frequency = (int) $integration->getAttribute('sync_frequency');
        
        if ($frequency <= 0) {
            return false;
        }

        $lastSync = $integration->getAttribute('last_sync_at');
        
        if (!$lastSync) {
            return true;
        }

        $nextSyncTime = strtotime($lastSync) + ($frequency * 60);
        
        return time() >= $nextSyncTime;
    }
}