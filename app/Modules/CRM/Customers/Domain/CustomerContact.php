<?php
// Path: app/Modules/CRM/Customers/Domain/CustomerContact.php

declare(strict_types=1);

namespace App\Modules\CRM\Customers\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Customer Contact
 * جهات الاتصال المتعددة المرتبطة بعميل واحد (مثال: مدير المشتريات للشركة العميلة، المحاسب، الخ).
 */
class CustomerContact extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'customer_id' => 'integer',
        'name'        => 'string',
        'job_title'   => 'string',
        'email'       => 'string',
        'phone'       => 'string',
        'mobile'      => 'string',
        'is_primary'  => 'boolean',
    ];
}