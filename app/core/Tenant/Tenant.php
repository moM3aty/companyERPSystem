<?php
// Path: app/Core/Tenant/Tenant.php

declare(strict_types=1);

namespace App\Core\Tenant;

/**
 * Enterprise Tenant Object
 * Represents the current active Company and Branch context for the incoming request.
 * Properties are readonly to prevent accidental state modification during the request lifecycle.
 */
class Tenant
{
    /**
     * The ID of the current active Company.
     *
     * @var int
     */
    public readonly int $companyId;

    /**
     * The ID of the current active Branch (if applicable).
     *
     * @var int|null
     */
    public readonly ?int $branchId;

    /**
     * The timezone configured for this tenant.
     *
     * @var string
     */
    public readonly string $timezone;

    /**
     * The default currency ID configured for this tenant.
     *
     * @var int|null
     */
    public readonly ?int $currencyId;

    /**
     * Tenant constructor.
     *
     * @param int $companyId
     * @param int|null $branchId
     * @param string $timezone
     * @param int|null $currencyId
     */
    public function __construct(
        int $companyId,
        ?int $branchId = null,
        string $timezone = 'Asia/Riyadh',
        ?int $currencyId = null
    ) {
        $this->companyId = $companyId;
        $this->branchId = $branchId;
        $this->timezone = $timezone;
        $this->currencyId = $currencyId;
    }

    /**
     * Check if the tenant is operating within a specific branch.
     *
     * @return bool
     */
    public function hasBranch(): bool
    {
        return $this->branchId !== null;
    }
}