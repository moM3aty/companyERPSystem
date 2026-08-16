<?php
// Path: app/Core/Approval/Http/Controllers/ApprovalController.php

declare(strict_types=1);

namespace App\Core\Approval\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Core\Approval\ApprovalEngine;
use App\Core\Approval\ApprovalDecision;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Approval Workflow
 * نقطة تفاعل المدراء مع النظام لاعتماد أو رفض المستندات (فواتير، طلبات شراء، إجازات).
 */
class ApprovalController extends Controller
{
    protected ApprovalEngine $approvalEngine;
    protected DatabaseManager $db;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        ApprovalEngine $approvalEngine,
        DatabaseManager $db,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->approvalEngine = $approvalEngine;
        $this->db = $db;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;

        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * جلب الطلبات المعلقة التي تنتظر موافقة المستخدم الحالي.
     */
    public function pending(): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        // استعلام معقد يجلب الطلبات التي تخص المستخدم مباشرة أو تخص الدور (Role) الذي يلعبه
        $sql = "
            SELECT r.id as request_id, r.document_type, r.document_id, r.created_at, s.id as step_id, s.sla_deadline_at
            FROM approval_steps s
            JOIN approval_requests r ON s.approval_request_id = r.id
            LEFT JOIN user_roles ur ON s.role_id = ur.role_id
            LEFT JOIN approval_delegations ad ON s.approver_id = ad.delegator_user_id AND ad.is_active = 1 AND NOW() BETWEEN ad.start_date AND ad.end_date
            WHERE r.company_id = ? 
              AND s.is_current = 1 
              AND s.status = 'pending'
              AND (
                  s.approver_id = ? OR 
                  ur.user_id = ? OR 
                  ad.delegate_user_id = ?
              )
            ORDER BY s.created_at ASC
        ";

        $requests = $this->db->connection()->select($sql, [$companyId, $userId, $userId, $userId]);

        return ApiResponse::success($requests);
    }

    /**
     * تسجيل قرار المدير (موافقة أو رفض).
     */
    public function decide(Request $request, int $requestId): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        $action = $cleanData['action'] ?? ''; // 'approve' or 'reject'
        $comments = $cleanData['comments'] ?? '';

        $decision = new ApprovalDecision($requestId, $userId, $action, $comments);

        $this->approvalEngine->processDecision($decision, $companyId);

        $msg = $action === 'approve' ? "Request approved successfully." : "Request rejected and closed.";
        
        return ApiResponse::success(null, $msg);
    }
}