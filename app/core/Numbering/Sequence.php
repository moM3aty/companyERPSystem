<?php
// Path: app/Core/Numbering/Sequence.php

declare(strict_types=1);

namespace App\Core\Numbering;

use App\Core\Models\Entity;

/**
 * Enterprise Sequence Entity
 * يمثل سجل التسلسل في قاعدة البيانات (document_sequences).
 */
class Sequence extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'current_value' => 'integer',
        'step' => 'integer',
        'reset_yearly' => 'boolean',
        'reset_monthly' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * الحصول على النمط ككائن Pattern.
     *
     * @return NumberPattern
     */
    public function getPatternObject(): NumberPattern
    {
        return new NumberPattern($this->getAttribute('pattern') ?: '{SEQ}');
    }
}