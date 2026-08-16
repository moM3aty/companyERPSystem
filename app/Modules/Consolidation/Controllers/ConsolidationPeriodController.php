<?php
// File 10: app/Modules/Consolidation/Controllers/ConsolidationPeriodController.php
declare(strict_types=1);

namespace App\Modules\Consolidation\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\Consolidation\Services\ConsolidatedReportService;
use App\Core\Authorization\Gate;

class ConsolidationPeriodController extends Controller
{
    protected ConsolidatedReportService $reportService;
    protected Gate $gate;

    public function __construct(ConsolidatedReportService $reportService, Gate $gate)
    {
        $this->reportService = $reportService;
        $this->gate = $gate;
        $this->middleware(['api', 'auth', 'tenant']); // Group-level authorization applies
    }

    public function generateReport(Request $request, int $periodId): JsonResponse
    {
        $this->gate->authorize('consolidation', 'reports', 'view');

        $report = $this->reportService->generateConsolidatedTrialBalance($periodId);

        return ApiResponse::success(['consolidated_trial_balance' => $report], 'Consolidated report generated successfully.');
    }
}