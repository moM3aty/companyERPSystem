<?php
// Path: app/Modules/Purchasing/RFQ/Application/RfqService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Application;

use App\Core\Database\TransactionManager;
use App\Modules\Purchasing\RFQ\Domain\RfqRepositoryInterface;
use App\Core\Tenant\TenantContext;

class RfqService
{
    protected RfqRepositoryInterface $rfqRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        RfqRepositoryInterface $rfqRepo,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->rfqRepo = $rfqRepo;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function createRfq(array $headerData, array $itemsData, array $supplierIds, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $itemsData, $supplierIds, $userId) {
            
            $rfqData = [
                'company_id'    => $companyId,
                'branch_id'     => $branchId,
                'rfq_number'    => $this->rfqRepo->generateRfqNumber($companyId),
                'title'         => $headerData['title'],
                'deadline_date' => $headerData['deadline_date'],
                'delivery_date' => $headerData['delivery_date'] ?? null,
                'status'        => 'sent', // نعتبره أُرسل للموردين فوراً للتبسيط
                'created_by'    => $userId,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $rfqId = $this->rfqRepo->create($rfqData);

            $this->rfqRepo->bulkInsertItems($rfqId, $itemsData);
            $this->rfqRepo->bulkInsertSuppliers($rfqId, array_unique($supplierIds));

            // هنا يمكن إطلاق EventBus لإرسال إيميلات للموردين بطلب التسعير (Send RFQ Emails)

            return $rfqId;
        });
    }
}