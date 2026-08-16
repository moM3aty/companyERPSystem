<?php
// Path: app/Modules/FixedAssets/Transfers/Http/Controllers/TransferController.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Transfers\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\FixedAssets\Transfers\Application\TransferService;
use App\Modules\FixedAssets\Transfers\Http\Requests\StoreTransferRequest;

class TransferController extends Controller
{
    protected TransferService $transferService;
    protected Gate $gate;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        TransferService $transferService,
        Gate $gate,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->transferService = $transferService;
        $this->gate = $gate;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreTransferRequest $validator): JsonResponse
    {
        $this->gate->authorize('fixed_assets', 'transfers', 'create');

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $this->auth->user()->companyId);

        $transferId = $this->transferService->executeTransfer($validatedData, $this->auth->user()->id);

        return ApiResponse::created(['transfer_id' => $transferId], 'Asset transferred successfully.');
    }
}