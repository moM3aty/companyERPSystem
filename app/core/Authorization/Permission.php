<?php
// Path: app/Core/Authorization/Permission.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Models\Entity;

/**
 * Enterprise Permission Entity
 * يمثل صلاحية ذرية (Atomic Permission) للوصول إلى مورد معين.
 * مثال: module='sales', resource='invoice', action='approve'
 */
class Permission extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'module'      => 'string',
        'resource'    => 'string',
        'action'      => 'string',
        'description' => 'string',
    ];

    /**
     * تحويل الصلاحية إلى المفتاح النصي القياسي.
     *
     * @return string
     */
    public function getKey(): string
    {
        return "{$this->module}.{$this->resource}.{$this->action}";
    }
}