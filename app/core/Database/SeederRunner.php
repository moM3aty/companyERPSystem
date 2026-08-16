<?php
// Path: app/Core/Database/SeederRunner.php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Contracts\LoggerInterface;
use Throwable;

/**
 * Enterprise Seeder Runner
 * المحرك المسؤول عن تشغيل ملفات الـ Seeders بأمان وتسجيل النتائج.
 */
class SeederRunner
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * تشغيل Seeder معين بالاسم.
     *
     * @param string $seederClass
     * @return bool
     */
    public function run(string $seederClass): bool
    {
        try {
            if (!class_exists($seederClass)) {
                $this->logger->error("Seeder class [{$seederClass}] not found.");
                return false;
            }

            /** @var Seeder $seeder */
            $seeder = new $seederClass($this->db);
            
            $this->logger->info("Seeding database using: {$seederClass}");
            $seeder->run();
            $this->logger->info("Successfully seeded: {$seederClass}");
            
            return true;
        } catch (Throwable $e) {
            $this->logger->critical("Seeding failed for [{$seederClass}]. Error: " . $e->getMessage());
            return false;
        }
    }
}