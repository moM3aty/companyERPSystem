<?php
// Path: app/Core/Workflow/Approval/ApprovalMatrix.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Approval Matrix
 * يحدد من هم الأشخاص أو الأدوار المطلوبة للموافقة بناءً على نوع المستند وقيمته (مثال: مبلغ الفاتورة).
 */
class ApprovalMatrix
{
    protected DatabaseManager $db;
    protected ConditionalApproval $conditionalApproval;

    public function __construct(DatabaseManager $db, ConditionalApproval $conditionalApproval)
    {
        $this->db = $db;
        $this->conditionalApproval = $conditionalApproval;
    }

    /**
     * جلب سلسلة الموافقات (Approval Chain) لمستند معين بناءً على قيمته.
     *
     * @param string $documentType (مثال: purchase_order)
     * @param array $payload البيانات المالية أو الإدارية لتقييمها
     * @param int $companyId
     * @return array مصفوفة مرتبة بمستويات الموافقة
     */
    public function determineApprovalChain(string $documentType, array $payload, int $companyId): array
    {
        $sql = "SELECT * FROM approval_matrices 
                WHERE document_type = ? AND company_id = ? AND is_active = 1 
                ORDER BY level ASC";
                
        $matrixRules = $this->db->connection()->select($sql, [$documentType, $companyId]);
        
        $approvalChain = [];

        foreach ($matrixRules as $rule) {
            $conditions = json_decode($rule['conditions'], true) ?? [];
            
            // تقييم ما إذا كان هذا المستوى ينطبق على هذا المستند (مثال: Amount > 10,000)
            if ($this->conditionalApproval->evaluate($conditions, $payload)) {
                $approvalChain[] = [
                    'level'       => (int) $rule['level'],
                    'role_id'     => $rule['role_id'] ? (int) $rule['role_id'] : null,
                    'approver_id' => $rule['approver_id'] ? (int) $rule['approver_id'] : null,
                    'is_parallel' => (bool) $rule['is_parallel'],
                    'sla_hours'   => (int) $rule['sla_hours'],
                ];
            }
        }

        return $approvalChain;
    }
}