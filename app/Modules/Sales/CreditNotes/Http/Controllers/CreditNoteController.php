<?php
// Path: app/Modules/Sales/CreditNotes/Http/Controllers/CreditNoteController.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Sales\CreditNotes\Application\CreditNoteService;
use App\Modules\Sales\CreditNotes\Http\Requests\StoreCreditNoteRequest;

class CreditNoteController extends Controller
{
    protected CreditNoteService $cnService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        CreditNoteService $cnService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->cnService = $cnService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreCreditNoteRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'credit_notes', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $headerData = [
            'customer_id' => $validatedData['customer_id'],
            'invoice_id'  => $validatedData['invoice_id'] ?? null,
            'note_date'   => $validatedData['note_date'],
            'currency_id' => $validatedData['currency_id'],
            'reason'      => $validatedData['reason'] ?? '',
        ];

        $cnId = $this->cnService->createCreditNote($headerData, $validatedData['items'], $userId);

        return ApiResponse::created(['credit_note_id' => $cnId], 'Credit Note processed successfully. AR reversed and stock returned.');
    }
}