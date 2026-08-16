<?php
// Path: app/Core/Database/Seeder.php

declare(strict_types=1);

namespace App\Core\Database;

/**
 * Enterprise Database Seeder Base
 * الفئة الأساسية التي ترث منها ملفات ضخ البيانات (Seeding).
 * تستخدم لإدخال البيانات الأساسية (Master Data) أو البيانات الوهمية (Dummy Data) للاختبار.
 */
abstract class Seeder
{
    protected DatabaseManager $db;

    /**
     * Seeder constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تنفيذ عملية ضخ البيانات.
     * يجب على الكلاسات الوارثة تنفيذ هذه الدالة.
     *
     * @return void
     */
    abstract public function run(): void;

    /**
     * استدعاء Seeder آخر من داخل الـ Seeder الحالي للحفاظ على الترتيب.
     *
     * @param string $seederClass
     * @return void
     */
    protected function call(string $seederClass): void
    {
        /** @var Seeder $seeder */
        $seeder = new $seederClass($this->db);
        $seeder->run();
    }
}