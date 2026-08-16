<?php
// Path: app/Modules/Maintenance/Services/Http/Controllers/ServiceController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\Services\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

class ServiceController extends Controller
{
    protected DatabaseManager $db;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, Gate $gate, TenantContext $tenant)
    {
        $this->db = $db;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('maintenance', 'services', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $services = $this->db->connection()->select("SELECT * FROM maintenance_services WHERE company_id = ?", [$companyId]);

        return ApiResponse::success(['services' => $services]);
    }
}