<?php
// Path: app/Modules/Intercompany/Controllers/ReconciliationController.php
declare(strict_types=1);

namespace App\Modules\Intercompany\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Intercompany\Services\ReconciliationService;
use App\Modules\Intercompany\Services\MatchingService;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;

class ReconciliationController extends Controller
{
    protected ReconciliationService $reconService;
    protected MatchingService $matchingService;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ReconciliationService $reconService, 
        MatchingService $matchingService, 
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->reconService = $reconService;
        $this->matchingService = $matchingService;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']); 
    }

    public function runMatch(Request $request, int $periodId, int $companyA, int $companyB): JsonResponse
    {
        $result = $this->matchingService->runMatching($periodId, $companyA, $companyB);
        return ApiResponse::success($result, 'Matching executed successfully.');
    }

    public function generateDocument(Request $request): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $docId = $this->reconService->generateReconciliationDocument(
            (int)$data['period_id'],
            (int)$data['company_a_id'],
            (int)$data['company_b_id'],
            $this->auth->user()->id
        );

        return ApiResponse::created(['reconciliation_id' => $docId], 'Reconciliation document generated.');
    }
}