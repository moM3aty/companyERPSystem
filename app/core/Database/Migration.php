<?php
// Path: app/Core/Database/Migration.php

declare(strict_types=1);

namespace App\Core\Database;

/**
 * Enterprise Database Migration Base
 * الفئة الأساسية التي ترث منها جميع ملفات بناء وتعديل جداول قاعدة البيانات.
 */
abstract class Migration
{
    /**
     * @var Connection
     */
    protected Connection $connection;

    /**
     * Migration constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * تنفيذ التعديلات أو إنشاء الجداول (Run the migrations).
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * التراجع عن التعديلات أو حذف الجداول (Reverse the migrations).
     *
     * @return void
     */
    abstract public function down(): void;
}