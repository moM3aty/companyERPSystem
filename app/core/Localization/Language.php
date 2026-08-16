<?php
// Path: app/Core/Localization/Language.php

declare(strict_types=1);

namespace App\Core\Localization;

use App\Core\Models\Entity;

/**
 * Enterprise Language Entity
 * يمثل لغة مدعومة في النظام (مثال: العربية، الإنجليزية).
 * يحدد اتجاه الواجهة (RTL/LTR) وحالة التفعيل.
 */
class Language extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'code'       => 'string',  // e.g., 'ar', 'en', 'fr'
        'name'       => 'string',  // e.g., 'Arabic', 'English'
        'native_name'=> 'string',  // e.g., 'العربية'
        'is_rtl'     => 'boolean', // Right-to-Left support
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * التحقق مما إذا كانت اللغة تكتب من اليمين لليسار.
     *
     * @return bool
     */
    public function isRtl(): bool
    {
        return $this->getAttribute('is_rtl') === true;
    }
}