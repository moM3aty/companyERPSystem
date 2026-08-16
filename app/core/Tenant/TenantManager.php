<?php
// Path: app/Core/Tenant/TenantManager.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Http\Request;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Tenant Manager
 * Coordinates the resolution and application of the Tenant context across the application.
 */
class TenantManager
{
    protected TenantContext $context;
    protected TenantResolver $resolver;

    /**
     * TenantManager constructor.
     *
     * @param TenantContext $context
     * @param TenantResolver $resolver
     */
    public function __construct(TenantContext $context, TenantResolver $resolver)
    {
        $this->context = $context;
        $this->resolver = $resolver;
    }

    /**
     * Attempt to initialize the Tenant context from the Request.
     *
     * @param Request $request
     * @return bool True if tenant was resolved, false otherwise.
     */
    public function initialize(Request $request): bool
    {
        $tenant = $this->resolver->resolve($request);

        if ($tenant !== null) {
            $this->context->setTenant($tenant);
            return true;
        }

        return false;
    }

    /**
     * Enforce that a Tenant exists for the current request.
     * Used by Middlewares to protect specific routes.
     *
     * @throws AuthorizationException
     * @return void
     */
    public function enforceTenantExists(): void
    {
        if (!$this->context->hasTenant()) {
            throw new AuthorizationException(
                "Access Denied: Missing or invalid Company context. Please select a valid company.",
                401
            );
        }
    }

    /**
     * Switch the current active tenant programmatically.
     * (E.g., An admin switching views between companies).
     *
     * @param Tenant $tenant
     * @return void
     */
    public function switchTenant(Tenant $tenant): void
    {
        $this->context->setTenant($tenant);
        
        // Also update session if applicable, keeping state consistent
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['company_id'] = $tenant->companyId;
            $_SESSION['branch_id'] = $tenant->branchId;
        }
    }

    /**
     * Get the current initialized Tenant.
     *
     * @return Tenant|null
     */
    public function getCurrentTenant(): ?Tenant
    {
        return $this->context->getTenant();
    }
}