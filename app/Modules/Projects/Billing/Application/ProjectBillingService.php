<?php
// Path: app/Modules/Projects/Billing/Application/ProjectBillingService.php

declare(strict_types=1);

namespace App\Modules\Projects\Billing\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Sales\Services\InvoiceService;

/**
 * Enterprise Application Service: Project Billing
 * يجمع الساعات غير المفوترة والمراحل המكتملة للمشروع، ويقوم بتوليد فاتورة مبيعات (Sales Invoice) آلياً للعميل.
 */
class ProjectBillingService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected InvoiceService $invoiceService;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, InvoiceService $invoiceService)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->invoiceService = $invoiceService;
    }

    public function generateInvoiceForProject(int $projectId, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($projectId, $companyId, $userId) {
            
            $project = $this->db->connection()->selectOne(
                "SELECT customer_id, name FROM projects WHERE id = ? AND company_id = ? AND status = 'active'",
                [$projectId, $companyId]
            );

            if (!$project || !$project['customer_id']) {
                throw new BusinessException("Project is inactive or has no linked customer to bill.");
            }

            // 1. جلب المراحل المكتملة ولم تُفوتر بعد
            $milestones = $this->db->connection()->select(
                "SELECT id, name, billing_amount FROM project_milestones WHERE project_id = ? AND status = 'achieved' AND is_billed = 0",
                [$projectId]
            );

            if (empty($milestones)) {
                throw new BusinessException("No unbilled achieved milestones found for this project.");
            }

            $itemsData = [];
            $milestoneIds = [];

            // للتبسيط نفترض وجود منتج افتراضي للخدمات باسم 'Project Service' يحمل الـ ID رقم 1
            $serviceProductId = 1; 

            foreach ($milestones as $milestone) {
                $itemsData[] = [
                    'product_id' => $serviceProductId,
                    'description' => "Project Milestone: {$milestone['name']} ({$project['name']})",
                    'quantity' => 1,
                    'unit_price' => (float) $milestone['billing_amount'],
                ];
                $milestoneIds[] = $milestone['id'];
            }

            $headerData = [
                'customer_id' => $project['customer_id'],
                'invoice_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+15 days')),
                'currency_id' => 1, // Default Base Currency
            ];

            // 2. إصدار فاتورة المبيعات عبر محرك המبيعات المركزي
            $invoiceId = $this->invoiceService->createInvoice($headerData, $itemsData, $userId);

            // 3. تعليم المراحل كـ "مفوترة"
            $placeholders = implode(',', array_fill(0, count($milestoneIds), '?'));
            $this->db->connection()->update(
                "UPDATE project_milestones SET is_billed = 1, linked_invoice_id = ? WHERE id IN ({$placeholders})",
                array_merge([$invoiceId], $milestoneIds)
            );

            return $invoiceId;
        });
    }
}