<?php
// Path: app/Modules/Maintenance/WorkOrders/Infrastructure/WorkOrderRepository.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Maintenance\WorkOrders\Domain\WorkOrderRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Work Order
 */
class WorkOrderRepository extends BaseRepository implements WorkOrderRepositoryInterface
{
    protected string $table = 'maintenance_work_orders';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    
    /**
     * @inheritDoc
     */
    public function generateWorkOrderNumber(int $companyId): string
    {
        $prefix = 'WO-' . date('ym') . '-';
        
        $lastOrder = $this->newQuery()
            ->select(['work_order_number'])
            ->where('company_id', '=', $companyId)
            ->where('work_order_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastOrder['work_order_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}