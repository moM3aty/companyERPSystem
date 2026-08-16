<?php
// Path: app/DisasterRecovery/Backup/Domain/BackupRecord.php

declare(strict_types=1);

namespace App\DisasterRecovery\Backup\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Backup Record
 * يسجل تاريخ وحالة كل عملية نسخ احتياطي لقاعدة البيانات لضمان المراقبة الدقيقة للـ Disaster Recovery.
 */
class BackupRecord extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'            => 'integer',
        'file_path'     => 'string',
        'file_size'     => 'integer', // Size in bytes
        'type'          => 'string',  // 'full_db', 'incremental', 'files'
        'status'        => 'string',  // 'success', 'failed', 'in_progress'
        'error_message' => 'string',
        'created_at'    => 'string',
        'updated_at'    => 'string',
    ];
}