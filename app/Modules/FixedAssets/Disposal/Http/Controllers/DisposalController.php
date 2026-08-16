<?php
// Path: app/Modules/FixedAssets/Disposal/Http/Controllers/DisposalController.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Disposal\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\FixedAssets\Disposal\Application\DisposalService;
use App\Modules\FixedAssets\Disposal\Http\Requests\StoreDisposalRequest;

class DisposalController extends Controller
{
    protected DisposalService $disposalService;
    protected Gate $gate;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        DisposalService $disposalService,
        Gate $gate,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->disposalService = $disposalService;
        $this->gate = $gate;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreDisposalRequest $validator): JsonResponse
    {
        $this->gate->authorize('fixed_assets', 'disposal', 'create');

        $cleanData = $this->inputGuard->getCleanPayload($request);
        // $companyId is automatically injected in the validator via TenantContext in a real flow
        $validatedData = $validator->validate($cleanData, $this->auth->user()->companyId);

        $disposalId = $this->disposalService->disposeAsset($validatedData, $this->auth->user()->id);

        return ApiResponse::created(['disposal_id' => $disposalId], 'Asset disposed successfully and accounting entries posted.');
    }
}