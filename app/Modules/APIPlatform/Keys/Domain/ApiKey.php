<?php
// Path: app/Modules/APIPlatform/Keys/Domain/ApiKey.php

declare(strict_types=1);

namespace App\Modules\APIPlatform\Keys\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise API Key Entity
 * يمثل مفتاح تواصل (API Key) مُصدر لعميل أو تطبيق خارجي للربط بالـ ERP.
 */
class ApiKey extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'name'           => 'string', // اسم التطبيق المستفيد
        'key_hash'       => 'string', // المفتاح المشفر (لا يُحفظ صريحاً)
        'last_used_at'   => 'string',
        'expires_at'     => 'string',
        'allowed_ips'    => 'json',   // تحديد IPs معينة مسموح لها باستخدام هذا المفتاح
        'scopes'         => 'json',   // الصلاحيات (مثال: ['sales.read', 'inventory.write'])
        'is_active'      => 'boolean',
        'created_by'     => 'integer',
        'created_at'     => 'string',
    ];

    public function isValid(): bool
    {
        if (!$this->getAttribute('is_active')) {
            return false;
        }

        $expires = $this->getAttribute('expires_at');
        if ($expires && strtotime((string)$expires) < time()) {
            return false;
        }

        return true;
    }
}