<?php
// Path: app/Core/Models/Traits/HasTimestamps.php

declare(strict_types=1);

namespace App\Core\Models\Traits;

/**
 * Enterprise Model Trait: HasTimestamps
 * يضمن تحديث حقلي created_at و updated_at تلقائياً للموديل عند إنشائه أو تعديله.
 */
trait HasTimestamps
{
    /**
     * تحديث أوقات الإنشاء والتعديل قبل حفظ الكائن.
     * (يجب استدعاؤها يدوياً في الـ Repository قبل الـ Insert أو تحديث الـ Attributes).
     *
     * @return self
     */
    public function updateTimestamps(): self
    {
        $time = date('Y-m-d H:i:s');
        
        if ($this->getAttribute('created_at') === null) {
            $this->setAttribute('created_at', $time);
        }
        
        $this->setAttribute('updated_at', $time);
        
        return $this;
    }
}