<?php
// Path: app/Compliance/AuditPolicies/Domain/AuditPolicy.php

declare(strict_types=1);

namespace App\Compliance\AuditPolicies\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Compliance: Audit Policy
 * يحدد أي الجداول/الكيانات يجب أن تفعل لها ميزة التدقيق (Audit Logging) وأيها لا، 
 * لتخفيف الضغط عن قاعدة البيانات.
 */
class AuditPolicy extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'entity_table'     => 'string', // e.g., 'sales_invoices', 'users'
        'log_inserts'      => 'boolean',
        'log_updates'      => 'boolean',
        'log_deletes'      => 'boolean',
        'excluded_columns' => 'json',   // أعمدة لا نريد مراقبتها (مثل updated_at)
        'is_active'        => 'boolean',
    ];
}