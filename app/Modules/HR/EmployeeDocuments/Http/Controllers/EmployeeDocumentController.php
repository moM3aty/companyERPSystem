<?php
// Path: app/Modules/HR/EmployeeDocuments/Http/Controllers/EmployeeDocumentController.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeDocuments\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\HR\EmployeeDocuments\Application\EmployeeDocumentService;

class EmployeeDocumentController extends Controller
{
    protected EmployeeDocumentService $documentService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        EmployeeDocumentService $documentService, 
        Gate $gate, 
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->documentService = $documentService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function upload(Request $request): JsonResponse
    {
        $this->gate->authorize('hr', 'documents', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        // In a real scenario, extract $_FILES via request wrapper
        $fileInfo = $_FILES['document'] ?? null;
        if (!$fileInfo) {
            return \App\Core\Api\ApiError::error("No document file provided.", 422);
        }

        $data = $this->inputGuard->getCleanPayload($request);
        
        $docId = $this->documentService->uploadDocument($data, $fileInfo, $companyId);

        return ApiResponse::created(['document_id' => $docId], 'Employee document uploaded and linked successfully.');
    }
}