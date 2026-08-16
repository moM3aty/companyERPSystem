<?php
// Path: app/Modules/POS/Shifts/Http/Controllers/ShiftClosingController.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\POS\Shifts\Application\ShiftClosingService;
use App\Modules\POS\Shifts\Http\Requests\StoreShiftClosingRequest;

class ShiftClosingController extends Controller
{
    protected ShiftClosingService $shiftService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ShiftClosingService $shiftService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->shiftService = $shiftService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function close(Request $request, StoreShiftClosingRequest $validator, int $shiftId): JsonResponse
    {
        $this->gate->authorize('pos', 'shifts', 'close');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $result = $this->shiftService->closeShift($shiftId, (float) $validatedData['actual_cash_counted'], $companyId, $userId);

        $message = "Shift closed successfully. Expected: {$result['expected_cash']}, Actual: {$result['actual_cash']}. ";
        $message .= $result['difference'] == 0 ? "Perfect match." : "Difference registered in GL: {$result['difference']}.";

        return ApiResponse::success($result, $message);
    }
}