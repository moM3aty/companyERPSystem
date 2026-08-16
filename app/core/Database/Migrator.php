<?php
// Path: app/Core/Database/Migrator.php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Contracts\LoggerInterface;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Database Migrator
 * المحرك المسؤول عن تتبع وتنفيذ ملفات الـ Migrations بالترتيب، وإدارة جدول التتبع (migrations table).
 * يضمن عدم تكرار تنفيذ نفس الملف مرتين، وهو ضروري جداً لعمليات تحديث النظام للعملاء.
 */
class Migrator
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;
    protected string $migrationsPath;

    /**
     * Migrator constructor.
     *
     * @param DatabaseManager $db
     * @param LoggerInterface $logger
     * @param string $migrationsPath مسار مجلد ملفات الـ migrations
     */
    public function __construct(DatabaseManager $db, LoggerInterface $logger, string $migrationsPath)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->migrationsPath = rtrim($migrationsPath, '\/');
    }

    /**
     * إنشاء جدول التتبع إذا لم يكن موجوداً.
     *
     * @return void
     */
    public function prepareDatabase(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->db->connection()->statement($sql);
    }

    /**
     * تنفيذ جميع ملفات الـ Migrations الجديدة.
     *
     * @return array قائمة بأسماء الملفات التي تم تنفيذها.
     * @throws DatabaseException
     */
    public function run(): array
    {
        $this->prepareDatabase();

        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        
        $pending = array_diff($files, $executed);
        
        if (empty($pending)) {
            $this->logger->info("No pending migrations to run.");
            return [];
        }

        $batch = $this->getNextBatchNumber();
        $ran = [];

        foreach ($pending as $file) {
            $this->logger->info("Migrating: {$file}");
            
            $this->requireMigrationFile($file);
            
            $className = $this->resolveClassName($file);
            $migration = new $className($this->db->connection());

            try {
                $migration->up();
                $this->logMigration($file, $batch);
                $ran[] = $file;
                $this->logger->info("Migrated: {$file}");
            } catch (\Throwable $e) {
                $this->logger->error("Migration failed: {$file}. Error: " . $e->getMessage());
                throw new DatabaseException("Migration failed on {$file}", [], $e);
            }
        }

        return $ran;
    }

    /**
     * جلب قائمة الملفات التي تم تنفيذها مسبقاً.
     *
     * @return array
     */
    protected function getExecutedMigrations(): array
    {
        $rows = $this->db->connection()->select("SELECT migration FROM migrations ORDER BY batch ASC, migration ASC");
        return array_column($rows, 'migration');
    }

    /**
     * جلب جميع ملفات الـ Migrations من المجلد وترتيبها.
     *
     * @return array
     */
    protected function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*_*.php');
        
        if ($files === false) {
            return [];
        }

        $files = array_map('basename', $files);
        sort($files);
        
        return $files;
    }

    /**
     * استخراج رقم الـ Batch التالي.
     *
     * @return int
     */
    protected function getNextBatchNumber(): int
    {
        $result = $this->db->connection()->selectOne("SELECT MAX(batch) as max_batch FROM migrations");
        return ((int) ($result['max_batch'] ?? 0)) + 1;
    }

    /**
     * تسجيل عملية التنفيذ في جدول التتبع.
     *
     * @param string $file
     * @param int $batch
     * @return void
     */
    protected function logMigration(string $file, int $batch): void
    {
        $this->db->connection()->insert(
            "INSERT INTO migrations (migration, batch) VALUES (?, ?)", 
            [$file, $batch]
        );
    }

    /**
     * تضمين ملف الـ Migration.
     *
     * @param string $file
     * @return void
     */
    protected function requireMigrationFile(string $file): void
    {
        require_once $this->migrationsPath . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * استخراج اسم الـ Class من اسم الملف (مثال: 2026_01_01_000000_create_users_table.php -> CreateUsersTable)
     *
     * @param string $file
     * @return string
     */
    protected function resolveClassName(string $file): string
    {
        $name = str_replace('.php', '', $file);
        $parts = explode('_', $name);
        
        // إزالة أجزاء التاريخ (أول 4 أجزاء عادة YYYY_MM_DD_HHMMSS)
        $classParts = array_slice($parts, 4); 
        
        $className = '';
        foreach ($classParts as $part) {
            $className .= ucfirst($part);
        }

        return $className;
    }
}