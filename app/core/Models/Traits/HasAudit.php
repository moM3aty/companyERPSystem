<?php
// Path: app/Core/Models/Traits/HasAudit.php

declare(strict_types=1);

namespace App\Core\Models\Traits;

/**
 * Enterprise Audit Trait
 * Automatically extracts Before and After states of a model to feed the AuditLog system.
 */
trait HasAudit
{
    /**
     * Get the original values of the attributes that were changed.
     * (Corresponds to 'old_values' JSON column in audit_logs).
     *
     * @return array
     */
    public function getAuditOldValues(): array
    {
        $dirty = $this->getDirty(); // Requires the model to extend BaseModel
        $old = [];

        foreach ($dirty as $key => $value) {
            $old[$key] = $this->getOriginal($key);
        }

        return $old;
    }

    /**
     * Get the new values of the attributes that were changed.
     * (Corresponds to 'new_values' JSON column in audit_logs).
     *
     * @return array
     */
    public function getAuditNewValues(): array
    {
        return $this->getDirty();
    }

    /**
     * Determine if there are changes worth auditing.
     *
     * @return bool
     */
    public function hasAuditableChanges(): bool
    {
        return $this->isDirty();
    }
}