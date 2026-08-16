<?php
// Path: app/Core/Tenant/TenantContext.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Tenant Context
 * Holds the current Tenant state globally for the duration of the HTTP Request.
 * Accessed by Repositories and Loggers to automatically scope queries and audits.
 */
class TenantContext
{
    /**
     * The current active tenant.
     *
     * @var Tenant|null
     */
    protected ?Tenant $tenant = null;

    /**
     * Set the current active tenant for the request.
     *
     * @param Tenant $tenant
     * @return void
     */
    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        
        // Setup the default timezone for the application based on the tenant's settings
        date_default_timezone_set($tenant->timezone);
    }

    /**
     * Get the current active tenant.
     *
     * @return Tenant|null
     */
    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Check if a tenant has been set.
     *
     * @return bool
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Require a tenant to be set, or throw a critical exception.
     * Extremely useful for security to ensure no data is leaked globally.
     *
     * @return Tenant
     * @throws BusinessException
     */
    public function requireTenant(): Tenant
    {
        if ($this->tenant === null) {
            throw new BusinessException(
                'Tenant Context is missing. A valid Company ID is required to perform this action.',
                403
            );
        }

        return $this->tenant;
    }

    /**
     * Get the current Company ID safely.
     *
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->tenant?->companyId;
    }

    /**
     * Get the current Branch ID safely.
     *
     * @return int|null
     */
    public function getBranchId(): ?int
    {
        return $this->tenant?->branchId;
    }

    /**
     * Clear the current tenant context.
     * Useful for switching contexts or testing.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->tenant = null;
    }
}