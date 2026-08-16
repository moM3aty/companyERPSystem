<?php
// Path: app/Core/Tenant/TenantResolver.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Http\Request;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Tenant Resolver
 * Inspects incoming requests (API Headers, Sessions, or Cookies) to identify the target Tenant.
 */
class TenantResolver
{
    protected DatabaseManager $db;

    /**
     * TenantResolver constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * Resolve the Tenant from the incoming HTTP Request.
     *
     * @param Request $request
     * @return Tenant|null
     */
    public function resolve(Request $request): ?Tenant
    {
        // 1. Check API Headers (Ideal for Mobile Apps or Headless Frontends)
        $companyId = $request->server('HTTP_X_COMPANY_ID');
        $branchId = $request->server('HTTP_X_BRANCH_ID');

        // 2. Check Session (Ideal for Traditional Web Interfaces)
        if (empty($companyId) && isset($_SESSION['company_id'])) {
            $companyId = $_SESSION['company_id'];
            $branchId = $_SESSION['branch_id'] ?? null;
        }

        // If no identification is found, we cannot resolve a tenant.
        if (empty($companyId)) {
            return null;
        }

        return $this->fetchTenantData((int) $companyId, $branchId !== null ? (int) $branchId : null);
    }

    /**
     * Fetch the validated tenant details from the database.
     *
     * @param int $companyId
     * @param int|null $branchId
     * @return Tenant|null
     */
    protected function fetchTenantData(int $companyId, ?int $branchId): ?Tenant
    {
        // We use a raw query here to avoid circular dependencies with Repositories
        $query = "SELECT id, timezone, currency_id, status FROM companies WHERE id = ? AND status = 'active' AND deleted_at IS NULL LIMIT 1";
        
        $companyData = $this->db->connection()->selectOne($query, [$companyId]);

        if (!$companyData) {
            return null;
        }

        // Validate Branch if provided
        $validatedBranchId = null;
        if ($branchId !== null) {
            $branchQuery = "SELECT id FROM branches WHERE id = ? AND company_id = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1";
            $branchData = $this->db->connection()->selectOne($branchQuery, [$branchId, $companyId]);
            
            if ($branchData) {
                $validatedBranchId = (int) $branchData['id'];
            }
        }

        return new Tenant(
            (int) $companyData['id'],
            $validatedBranchId,
            $companyData['timezone'] ?? 'Asia/Riyadh',
            $companyData['currency_id'] ? (int) $companyData['currency_id'] : null
        );
    }
}