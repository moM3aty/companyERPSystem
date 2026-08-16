<?php
// Path: app/Modules/Maintenance/SpareParts/Http/Controllers/SparePartController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\SpareParts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;

class SparePartController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(Gate $gate, TenantContext $tenant)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    // هنا يتم بناء الـ Endpoints لاستعراض وتعديل قطع الغيار
    public function index(): JsonResponse
    {
        $this->gate->authorize('maintenance', 'spare_parts', 'view');
        return ApiResponse::success([], 'Spare parts retrieved.');
    }
}