<?php
// Path: app/Core/Models/Traits/HasTenant.php

declare(strict_types=1);

namespace App\Core\Models\Traits;

/**
 * Enterprise Tenant Trait
 * Provides helper methods to associate and validate models against the current Company ID.
 */
trait HasTenant
{
    /**
     * Get the name of the tenant foreign key column.
     *
     * @return string
     */
    public function getTenantColumn(): string
    {
        return 'company_id';
    }

    /**
     * Get the Tenant ID (Company ID) associated with this model.
     *
     * @return int|null
     */
    public function getTenantId(): ?int
    {
        $value = $this->getAttribute($this->getTenantColumn());
        
        return $value !== null ? (int) $value : null;
    }

    /**
     * Set the Tenant ID (Company ID) for this model.
     *
     * @param int $companyId
     * @return self
     */
    public function setTenantId(int $companyId): self
    {
        $this->setAttribute($this->getTenantColumn(), $companyId);
        
        return $this;
    }

    /**
     * Determine if this model belongs to the specified Tenant ID.
     * Very useful for authorization Gate checks.
     *
     * @param int $companyId
     * @return bool
     */
    public function belongsToTenant(int $companyId): bool
    {
        return $this->getTenantId() === $companyId;
    }
}