<?php
// Path: app/Compliance/DataRetention/Application/DataPurgeService.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Compliance: Data Purge Service
 * خدمة تعمل في الخلفية دورياً لمسح أو إخفاء هوية البيانات القديمة جداً بناءً على القوانين (Data Privacy).
 */
class DataPurgeService
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * تنفيذ دورة تنظيف البيانات لجميع الشركات.
     *
     * @return void
     */
    public function executePurge(): void
    {
        $this->logger->info("Starting Compliance Data Purge Process.");

        $policies = $this->db->connection()->select("SELECT * FROM compliance_retention_policies WHERE is_active = 1");

        foreach ($policies as $policy) {
            $days = (int) $policy['retention_days'];
            $entityType = $policy['entity_type'];
            $action = $policy['action_on_expiry'];
            $companyId = $policy['company_id'] ? (int) $policy['company_id'] : null;

            // تحديد تاريخ الانتهاء
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

            try {
                if ($action === 'delete') {
                    $this->purgeTable($entityType, $cutoffDate, $companyId);
                } elseif ($action === 'anonymize' && $entityType === 'customers') {
                    $this->anonymizeCustomers($cutoffDate, $companyId);
                }
            } catch (\Throwable $e) {
                $this->logger->error("Data Purge failed for {$entityType}: " . $e->getMessage());
            }
        }
        
        $this->logger->info("Compliance Data Purge Process Completed.");
    }

    /**
     * مسح السجلات القديمة من جداول اللوجز والبيانات المؤقتة.
     */
    protected function purgeTable(string $table, string $cutoffDate, ?int $companyId): void
    {
        // حماية أمنية لمنع مسح الجداول الأساسية بالغلط
        $allowedTables = ['audit_logs', 'activity_logs', 'failed_jobs', 'notifications', 'integration_logs'];
        
        if (!in_array($table, $allowedTables, true)) {
            return;
        }

        $sql = "DELETE FROM {$table} WHERE created_at < ?";
        $bindings = [$cutoffDate];

        if ($companyId) {
            $sql .= " AND company_id = ?";
            $bindings[] = $companyId;
        }

        $affected = $this->db->connection()->delete($sql, $bindings);
        $this->logger->info("Purged {$affected} records from {$table} older than {$cutoffDate}.");
    }

    /**
     * إخفاء هوية العملاء (GDPR Right to be Forgotten) للعملاء المحذوفين من فترة طويلة.
     */
    protected function anonymizeCustomers(string $cutoffDate, ?int $companyId): void
    {
        $sql = "UPDATE customers SET name = 'Anonymized', email = 'anon@deleted.local', phone = '000000000', tax_number = NULL 
                WHERE deleted_at IS NOT NULL AND deleted_at < ?";
        $bindings = [$cutoffDate];

        if ($companyId) {
            $sql .= " AND company_id = ?";
            $bindings[] = $companyId;
        }

        $affected = $this->db->connection()->update($sql, $bindings);
        if ($affected > 0) {
            $this->logger->info("Anonymized {$affected} deleted customers for compliance.");
        }
    }
}