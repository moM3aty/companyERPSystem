<?php
// Path: app/Modules/Purchasing/Returns/Http/Controllers/DebitNoteController.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Purchasing\Returns\Application\DebitNoteService;
use App\Modules\Purchasing\Returns\Http\Requests\StoreDebitNoteRequest;

class DebitNoteController extends Controller
{
    protected DebitNoteService $dnService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        DebitNoteService $dnService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->dnService = $dnService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreDebitNoteRequest $validator): JsonResponse
    {
        $this->gate->authorize('purchasing', 'returns', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'supplier_id'         => $validatedData['supplier_id'],
            'purchase_invoice_id' => $validatedData['purchase_invoice_id'] ?? null,
            'note_date'           => $validatedData['note_date'],
            'currency_id'         => $validatedData['currency_id'],
            'reason'              => $validatedData['reason'] ?? '',
        ];

        $dnId = $this->dnService->createDebitNote($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['debit_note_id' => $dnId], 'Debit Note generated successfully. Stock returned and AP adjusted.');
    }
}