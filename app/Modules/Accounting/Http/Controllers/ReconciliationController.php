<?php
// Path: app/Modules/Accounting/Http/Controllers/ReconciliationController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Modules\Accounting\Application\Services\ReconciliationService;
use App\Modules\Accounting\Http\Requests\ReconciliationRequest;
use Exception;

class ReconciliationController
{
    public function __construct(
        private readonly ReconciliationService $reconciliationService
    ) {}

    public function index(Request $request): void
    {
        require BASE_PATH . '/resources/views/accounting/reconciliation/index.php';
    }

    public function store(Request $request): Response
    {
        try {
            $companyId = 1;
            
            $dto = ReconciliationRequest::validateAndCreateDTO($request, $companyId);
            
            $success = $this->reconciliationService->processReconciliation($dto);

            return new Response(json_encode(['success' => $success]), 200, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            return new Response(json_encode(['success' => false, 'error' => $e->getMessage()]), 400, ['Content-Type' => 'application/json']);
        }
    }
}