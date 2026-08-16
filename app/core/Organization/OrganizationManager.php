<?php
// Path: app/Core/Organization/OrganizationManager.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Organization Manager
 * الواجهة المجمعة (Facade) للتعامل مع هيكل المنظمة بالكامل.
 */
class OrganizationManager
{
    public readonly OrganizationTree $tree;
    public readonly DepartmentManager $departments;
    public readonly CostCenterManager $costCenters;
    public readonly LocationManager $locations;

    public function __construct(
        OrganizationTree $tree,
        DepartmentManager $departments,
        CostCenterManager $costCenters,
        LocationManager $locations
    ) {
        $this->tree = $tree;
        $this->departments = $departments;
        $this->costCenters = $costCenters;
        $this->locations = $locations;
    }
}