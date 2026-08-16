<?php
// Path: app/Modules/Accounting/Http/Controllers/JournalEntryController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Infrastructure\Persistence\Repositories\JournalEntryRepository;
use App\Modules\Accounting\Http\Requests\CreateJournalEntryRequest;
use Exception;

class JournalEntryController
{
    public function __construct(
        private readonly JournalEntryService $journalEntryService,
        private readonly JournalEntryRepository $repository
    ) {}

    public function index(Request $request): void
    {
        $companyId = 1;
        $filters = $request->all();

        $entries = $this->repository->getAll($companyId, $filters);

        require BASE_PATH . '/resources/views/accounting/journal-entries/index.php';
    }

    public function create(Request $request): void
    {
        require BASE_PATH . '/resources/views/accounting/journal-entries/create.php';
    }

    public function store(Request $request): Response
    {
        try {
            $companyId = 1;
            $userId = 1; // From Auth session
            
            $dto = CreateJournalEntryRequest::validateAndCreateDTO($request, $companyId, $userId);
            
            $autoPost = $request->input('action') === 'post';

            $entryId = $this->journalEntryService->createAndPostEntry($dto, $autoPost);

            // Redirect back to index on standard web requests
            if (!$request->wantsJson()) {
                header('Location: /ERP/public/accounting/journal-entries');
                exit;
            }

            return new Response(json_encode(['success' => true, 'id' => $entryId]), 201, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            if (!$request->wantsJson()) {
                // In a real app, flash error to session and redirect back
                die("Error creating Journal Entry: " . $e->getMessage());
            }
            return new Response(json_encode(['success' => false, 'error' => $e->getMessage()]), 400, ['Content-Type' => 'application/json']);
        }
    }
}