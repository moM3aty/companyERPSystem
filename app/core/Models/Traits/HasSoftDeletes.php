<?php
// Path: app/Core/Models/Traits/HasSoftDeletes.php

declare(strict_types=1);

namespace App\Core\Models\Traits;

/**
 * Enterprise Model Trait: HasSoftDeletes
 * يوفر دوال مساعدة للتعامل مع السجلات التي تدعم الحذف الوهمي (Soft Delete).
 */
trait HasSoftDeletes
{
    /**
     * التحقق مما إذا كان السجل محذوفاً.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->getAttribute('deleted_at') !== null;
    }

    /**
     * تعيين السجل كمحذوف (تحديد وقت الحذف).
     *
     * @return self
     */
    public function markAsDeleted(): self
    {
        $this->setAttribute('deleted_at', date('Y-m-d H:i:s'));
        return $this;
    }
    
    /**
     * استعادة السجل المحذوف.
     *
     * @return self
     */
    public function restore(): self
    {
        $this->setAttribute('deleted_at', null);
        return $this;
    }
}