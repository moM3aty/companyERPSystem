<?php
// Path: app/DisasterRecovery/Backup/Application/DatabaseBackupService.php

declare(strict_types=1);

namespace App\DisasterRecovery\Backup\Application;

use App\Core\Config\Config;
use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;
use App\Core\Contracts\FileStorageInterface;
use RuntimeException;

/**
 * Enterprise Disaster Recovery: Database Backup Service
 * يقوم بإنشاء نسخة احتياطية (Dump) لقاعدة البيانات بالكامل، ضغطها، ورفعها للسحابة (S3/Local).
 */
class DatabaseBackupService
{
    protected Config $config;
    protected DatabaseManager $db;
    protected LoggerInterface $logger;
    protected FileStorageInterface $storage;

    public function __construct(Config $config, DatabaseManager $db, LoggerInterface $logger, FileStorageInterface $storage)
    {
        $this->config = $config;
        $this->db = $db;
        $this->logger = $logger;
        $this->storage = $storage;
    }

    /**
     * تشغيل عملية النسخ الاحتياطي (Full DB Dump).
     *
     * @return string مسار الملف المضغوط
     * @throws RuntimeException
     */
    public function runFullBackup(): string
    {
        $this->logger->info("Disaster Recovery: Starting Full Database Backup.");
        $recordId = $this->logBackupStart();

        $dbConfig = $this->config->get('database.connections.mysql');
        
        $host = escapeshellarg($dbConfig['host']);
        $user = escapeshellarg($dbConfig['username']);
        $pass = escapeshellarg($dbConfig['password']);
        $name = escapeshellarg($dbConfig['database']);
        
        $date = date('Y-m-d_H-i-s');
        $fileName = "backup_{$date}.sql.gz";
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        // استخدام mysqldump وضغطه فوراً باستخدام gzip لتقليل المساحة
        // تحذير: في بيئات الاستضافة قد تحتاج لمسار mysqldump الكامل
        $command = "mysqldump -h {$host} -u {$user} -p{$pass} {$name} --single-transaction --quick --lock-tables=false | gzip > {$tempPath}";

        $output = [];
        $returnVar = 0;
        
        // تنفيذ الأمر في بيئة السيرفر
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $error = "Backup failed with exit code: {$returnVar}";
            $this->logBackupEnd($recordId, 'failed', 0, '', $error);
            $this->logger->critical($error);
            throw new RuntimeException($error);
        }

        // رفع الملف إلى نظام التخزين (Local أو S3)
        $fileContent = file_get_contents($tempPath);
        $storagePath = 'backups/' . $fileName;
        
        $this->storage->put($storagePath, $fileContent);
        
        $fileSize = filesize($tempPath);
        unlink($tempPath); // تنظيف الملف المؤقت

        $this->logBackupEnd($recordId, 'success', $fileSize, $storagePath, '');
        $this->logger->info("Disaster Recovery: Backup completed successfully. Size: " . round($fileSize / 1024 / 1024, 2) . " MB.");

        return $storagePath;
    }

    protected function logBackupStart(): int
    {
        $this->db->connection()->insert(
            "INSERT INTO backup_records (type, status, created_at, updated_at) VALUES ('full_db', 'in_progress', ?, ?)",
            [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
        );
        return (int) $this->db->connection()->lastInsertId();
    }

    protected function logBackupEnd(int $id, string $status, int $size, string $path, string $error): void
    {
        $this->db->connection()->update(
            "UPDATE backup_records SET status = ?, file_size = ?, file_path = ?, error_message = ?, updated_at = ? WHERE id = ?",
            [$status, $size, $path, $error, date('Y-m-d H:i:s'), $id]
        );
    }
}