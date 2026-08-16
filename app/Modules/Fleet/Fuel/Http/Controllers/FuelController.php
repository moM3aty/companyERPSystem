<?php
// Path: app/Modules/Fleet/Fuel/Http/Controllers/FuelController.php

declare(strict_types=1);

namespace App\Modules\Fleet\Fuel\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;

class FuelController extends Controller
{
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(Gate $gate, TenantContext $tenant)
    {
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(): JsonResponse
    {
        $this->gate->authorize('fleet', 'fuel', 'view');
        return ApiResponse::success([], 'Fuel logs retrieved.');
    }
}