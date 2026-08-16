<?php
// Path: app/Core/Outbox/OutboxMessage.php

declare(strict_types=1);

namespace App\Core\Outbox;

use App\Core\Models\Entity;

/**
 * Enterprise Outbox Message Entity
 * يمثل الحدث (Event) المخزن في جدول الـ Outbox في قاعدة البيانات بانتظار المعالجة.
 */
class OutboxMessage extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'event_name' => 'string',
        'payload' => 'json', // يتم فك التشفير تلقائياً بفضل الـ Entity
        'is_processed' => 'boolean',
    ];
}