<?php
// Path: app/Core/Integrations/Integration.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Models\Entity;

/**
 * Enterprise Integration Entity
 * يمثل إعدادات ربط خارجي لشركة معينة (مثال: إعدادات ربط متجر Shopify مع الـ ERP للشركة رقم 1).
 */
class Integration extends Entity
{

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'provider'       => 'string', // e.g., 'shopify', 'zatca', 'stripe'
        'base_url'       => 'string',
        'credentials'    => 'json',   // (Client ID, Secret, API Keys) مشفرة في قاعدة البيانات
        'is_active'      => 'boolean',
        'sync_frequency' => 'integer', // معدل المزامنة بالدقائق
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];

    /**
     * استخراج بيانات الاعتماد بشكل آمن كـ Array.
     *
     * @return array
     */
    public function getCredentials(): array
    {
        $creds = $this->getAttribute('credentials');
        return is_array($creds) ? $creds : [];
    }
}