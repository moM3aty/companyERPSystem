<?php
// Path: app/Modules/Purchasing/Requisitions/Application/PurchaseRequisitionService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Application;

use App\Core\Database\TransactionManager;
use App\Modules\Purchasing\Requisitions\Domain\PurchaseRequisitionRepositoryInterface;
use App\Core\Tenant\TenantContext;

class PurchaseRequisitionService
{
    protected PurchaseRequisitionRepositoryInterface $prRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        PurchaseRequisitionRepositoryInterface $prRepo,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->prRepo = $prRepo;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function createRequisition(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        $totalEstimated = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) ($item['estimated_unit_price'] ?? 0.0);
            
            $lineTotal = round($qty * $price, 2);
            $totalEstimated += $lineTotal;

            $processedItems[] = [
                'product_id'           => (int) $item['product_id'],
                'description'          => $item['description'] ?? null,
                'quantity'             => $qty,
                'estimated_unit_price' => $price,
                'total_estimated'      => $lineTotal,
            ];
        }

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $totalEstimated, $userId) {
            
            $prData = [
                'company_id'      => $companyId,
                'branch_id'       => $branchId,
                'pr_number'       => $this->prRepo->generatePrNumber($companyId),
                'requester_id'    => $userId, // Defaulting to the logged in user
                'department_id'   => $headerData['department_id'],
                'request_date'    => date('Y-m-d'),
                'required_date'   => $headerData['required_date'],
                'justification'   => $headerData['justification'],
                'total_estimated' => $totalEstimated,
                'status'          => 'pending_approval', // يذهب لدورة الموافقات
                'created_by'      => $userId,
                'created_at'      => date('Y-m-d H:i:s')
            ];

            $prId = $this->prRepo->create($prData);

            $this->prRepo->bulkInsertItems($prId, $processedItems);

            // في النظام المتكامل: نقوم بإطلاق Approval Engine هنا لطلب موافقة مدير القسم!
            // $this->approvalEngine->start('purchase_requisition_flow', $prId, ...);

            return $prId;
        });
    }
}