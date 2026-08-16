<?php
// Path: app/Core/Integrations/Webhook.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Models\Entity;

/**
 * Enterprise Webhook Entity
 * يمثل نقطة نهاية (URL) خارجية مسجلة للاستماع لأحداث نظامنا.
 */
class Webhook extends Entity
{

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'event_name'  => 'string', // e.g., 'invoice.created'
        'target_url'  => 'string', // The external URL to hit
        'secret_key'  => 'string', // Used for HMAC SHA256 signature
        'is_active'   => 'boolean',
        'created_at'  => 'string',
    ];
}