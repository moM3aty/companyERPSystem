<?php
// Path: app/Modules/Maintenance/Application/MaintenanceService.php

declare(strict_types=1);

namespace App\Modules\Maintenance\Application;

use App\Modules\Maintenance\MaintenancePlans\Domain\MaintenancePlanRepositoryInterface;
use App\Modules\Maintenance\WorkOrders\Domain\WorkOrderRepositoryInterface;
use App\Modules\Maintenance\WorkOrders\Domain\Events\WorkOrderCompletedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Maintenance Engine
 * العقل المدبر لعمليات الصيانة. يربط الخطط بإنشاء الأوامر.
 */
class MaintenanceService
{
    protected MaintenancePlanRepositoryInterface $planRepo;
    protected WorkOrderRepositoryInterface $workOrderRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        MaintenancePlanRepositoryInterface $planRepo,
        WorkOrderRepositoryInterface $workOrderRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->planRepo = $planRepo;
        $this->workOrderRepo = $workOrderRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    public function createPlan(array $data, int $companyId, int $userId): int
    {
        $data['company_id'] = $companyId;
        $data['created_by'] = $userId;
        $data['status']     = 'active';
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->planRepo->create($data);
    }

    public function createWorkOrder(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            $data['company_id']        = $companyId;
            $data['work_order_number'] = $this->workOrderRepo->generateWorkOrderNumber($companyId);
            $data['status']            = 'pending';
            $data['created_by']        = $userId;
            $data['created_at']        = date('Y-m-d H:i:s');

            return $this->workOrderRepo->create($data);
        });
    }

    public function completeWorkOrder(int $workOrderId, float $actualCost, int $companyId): void
    {
        $this->transaction->execute(function () use ($workOrderId, $actualCost, $companyId) {
            $this->workOrderRepo->setTenantId($companyId);
            $workOrder = $this->workOrderRepo->findOrFail($workOrderId);

            if ($workOrder['status'] === 'completed') {
                throw new BusinessException("Work Order is already completed.");
            }

            $this->workOrderRepo->update($workOrderId, [
                'status'       => 'completed',
                'actual_cost'  => $actualCost,
                'completed_at' => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            // Fire Domain Event for Accounting and Asset Valuation modules
            $this->eventBus->publish(new WorkOrderCompletedEvent(
                $workOrderId, 
                $companyId, 
                (int) $workOrder['asset_id'], 
                $actualCost
            ));

            // If it originated from a plan, schedule the next one
            if (!empty($workOrder['maintenance_plan_id'])) {
                $this->scheduleNextPlanRun((int) $workOrder['maintenance_plan_id'], $companyId);
            }
        });
    }

    protected function scheduleNextPlanRun(int $planId, int $companyId): void
    {
        $this->planRepo->setTenantId($companyId);
        $plan = $this->planRepo->find($planId);
        
        if ($plan && $plan['status'] === 'active') {
            $frequency = (int) $plan['frequency_days'];
            $nextDate = date('Y-m-d', strtotime("+{$frequency} days"));
            
            $this->planRepo->update($planId, [
                'next_due_date' => $nextDate,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }
    }
}