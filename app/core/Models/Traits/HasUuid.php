<?php
// Path: app/Core/Models/Traits/HasUuid.php

declare(strict_types=1);

namespace App\Core\Models\Traits;

use App\Core\Helpers\Str;

/**
 * Enterprise Model Trait: HasUuid
 * يوفر توليد تلقائي للمعرفات الفريدة (UUID Version 4) للنماذج.
 * مهم جداً للـ APIs والتكاملات الخارجية التي لا تعتمد على الـ Auto-Increment ID لتجنب كشف حجم البيانات.
 */
trait HasUuid
{
    /**
     * توليد وتعيين UUID جديد للموديل إذا لم يكن يمتلك واحداً.
     *
     * @param string $column اسم عمود الـ UUID (الافتراضي uuid)
     * @return self
     */
    public function ensureUuidExists(string $column = 'uuid'): self
    {
        if (empty($this->getAttribute($column))) {
            $this->setAttribute($column, Str::uuid());
        }
        
        return $this;
    }

    /**
     * جلب قيمة الـ UUID.
     *
     * @param string $column
     * @return string|null
     */
    public function getUuid(string $column = 'uuid'): ?string
    {
        return $this->getAttribute($column);
    }
}