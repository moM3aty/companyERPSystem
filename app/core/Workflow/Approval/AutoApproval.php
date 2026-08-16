<?php
// Path: app/Core/Workflow/Approval/AutoApproval.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Auto Approval Engine
 * يعتمد الطلبات الصغيرة جداً أو الروتينية فوراً دون إزعاج المديرين لتسريع دورة العمل.
 */
class AutoApproval
{
    protected DatabaseManager $db;
    protected ApprovalMatrix $matrix;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, ApprovalMatrix $matrix, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->matrix = $matrix;
        $this->logger = $logger;
    }

    /**
     * فحص ما إذا كان المستند لا يتطلب أي موافقات بشرية بناءً على مصفوفة الصلاحيات (مثال: مبلغ أقل من 1000).
     *
     * @param string $documentType
     * @param array $payload
     * @param int $companyId
     * @return bool يُرجع True إذا كان يمكن اعتماده آلياً
     */
    public function evaluateAutoApproval(string $documentType, array $payload, int $companyId): bool
    {
        $chain = $this->matrix->determineApprovalChain($documentType, $payload, $companyId);

        // إذا عادت مصفوفة الموافقات فارغة، فهذا يعني أن الشروط لا تقتضي تدخل أي مدير، وبالتالي الاعتماد فوري!
        if (empty($chain)) {
            $this->logger->info("Auto-Approving document [{$documentType}] based on minimal threshold criteria.");
            return true;
        }

        return false;
    }
}