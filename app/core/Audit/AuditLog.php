<?php
// Path: app/Core/Audit/AuditLog.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Models\Entity;

/**
 * Enterprise Audit Log Entity
 * يمثل سجل التدقيق لحركة معينة تمت على البيانات (Insert, Update, Delete).
 * البيانات هنا غير قابلة للتعديل (Immutable) لضمان النزاهة.
 */
class AuditLog extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'user_id' => 'integer',
        'action' => 'string', // 'created', 'updated', 'deleted', 'restored'
        'entity_type' => 'string', // e.g., 'sales_invoices'
        'entity_id' => 'integer',
        'old_values' => 'json',
        'new_values' => 'json',
        'ip_address' => 'string',
        'user_agent' => 'string',
    ];
}