<?php
// Path: app/Core/Queue/FailedJob.php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Models\Entity;

/**
 * Enterprise Failed Job Entity
 * يمثل سجل المهمة التي فشلت نهائياً وتم نقلها إلى جدول failed_jobs.
 */
class FailedJob extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'failed_at' => 'string',
    ];
}