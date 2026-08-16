<?php
// Path: app/Modules/Accounting/Http/Controllers/ChartOfAccountsController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Modules\Accounting\Application\Services\AccountService;
use App\Modules\Accounting\Http\Requests\CreateAccountRequest;
use App\Modules\Accounting\Http\Resources\AccountResource;
use Exception;

class ChartOfAccountsController
{
    public function __construct(
        private readonly AccountService $accountService
    ) {}

    public function index(Request $request): void
    {
        $companyId = 1; 

        // Fetch structured tree and stats from Application Service
        $data = $this->accountService->getChartOfAccountsTree($companyId);
        
        $accountsTree = $data;
        // Mock stats for UI
        $stats = ['total' => 142, 'assets' => 45, 'liabilities' => 32, 'equity' => 8, 'revenue' => 15, 'expenses' => 42];

        require BASE_PATH . '/resources/views/accounting/chart-of-accounts/index.php';
    }

    public function create(Request $request): void
    {
        require BASE_PATH . '/resources/views/accounting/chart-of-accounts/create.php';
    }

    public function store(Request $request): Response
    {
        try {
            $companyId = 1;
            
            // Validate and extract DTO
            $dto = CreateAccountRequest::validateAndCreateDTO($request, $companyId);
            
            // Execute Business Logic
            $accountId = $this->accountService->createAccount($dto);

            return new Response(json_encode([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => ['id' => $accountId]
            ]), 201, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            return new Response(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]), 400, ['Content-Type' => 'application/json']);
        }
    }
}