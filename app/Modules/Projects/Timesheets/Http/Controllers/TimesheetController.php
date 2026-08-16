<?php
// Path: app/Modules/Projects/Timesheets/Http/Controllers/TimesheetController.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Projects\Timesheets\Application\TimesheetService;
use App\Modules\Projects\Timesheets\Http\Requests\StoreTimesheetRequest;

/**
 * Enterprise API Controller: Project Timesheets
 */
class TimesheetController extends Controller
{
    protected TimesheetService $timesheetService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        TimesheetService $timesheetService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->timesheetService = $timesheetService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreTimesheetRequest $validator): JsonResponse
    {
        $this->gate->authorize('projects', 'timesheets', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $timesheetId = $this->timesheetService->logTime($validatedData, $companyId);

        return ApiResponse::created(['timesheet_id' => $timesheetId], 'Time logged successfully to the project task.');
    }
}