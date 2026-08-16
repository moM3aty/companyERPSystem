<?php
// Path: app/Modules/Inventory/StockTaking/Application/StockTakingService.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Application;

use App\Modules\Inventory\StockTaking\Domain\StockCountRepositoryInterface;
use App\Modules\Inventory\Stock\Domain\StockRepositoryInterface;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Stock Taking
 * يعالج الفروقات بين الرصيد الدفتري والفعلي ويقوم بإجراء حركات (Adjustment) أوتوماتيكية عبر الـ StockService لضبط المخزن.
 */
class StockTakingService
{
    protected StockCountRepositoryInterface $countRepo;
    protected StockRepositoryInterface $stockRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        StockCountRepositoryInterface $countRepo,
        StockRepositoryInterface $stockRepo,
        StockService $stockService,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->countRepo = $countRepo;
        $this->stockRepo = $stockRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function processStockCount(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $itemsData, $userId) {
            $warehouseId = (int) $headerData['warehouse_id'];
            $countNo = $this->countRepo->generateCountNumber($companyId);

            $countData = [
                'company_id'   => $companyId,
                'branch_id'    => $branchId,
                'warehouse_id' => $warehouseId,
                'count_number' => $countNo,
                'count_date'   => $headerData['count_date'],
                'status'       => 'completed', // نعتمد الجرد مباشرة
                'created_by'   => $userId,
                'approved_by'  => $userId,
                'created_at'   => date('Y-m-d H:i:s')
            ];

            $countId = $this->countRepo->create($countData);
            $processedItems = [];

            foreach ($itemsData as $item) {
                $productId = (int) $item['product_id'];
                $countedQty = (float) $item['counted_quantity'];

                // جلب الرصيد والتكلفة المسجلة حالياً
                $currentStock = $this->stockRepo->lockForUpdate($productId, $warehouseId, $companyId);
                
                $systemQty = $currentStock ? (float) $currentStock->getAttribute('quantity') : 0.0;
                $unitCost = $currentStock ? (float) $currentStock->getAttribute('average_cost') : 0.0;
                
                $difference = $countedQty - $systemQty;

                $processedItems[] = [
                    'product_id'       => $productId,
                    'system_quantity'  => $systemQty,
                    'counted_quantity' => $countedQty,
                    'difference'       => $difference,
                    'unit_cost'        => $unitCost,
                ];

                // إذا كان هناك عجز (صرف)، أو زيادة (إضافة)
                if ($difference !== 0.0) {
                    $movementType = $difference > 0 ? StockMovementType::IN : StockMovementType::OUT;
                    
                    $this->stockService->recordMovement(
                        $productId,
                        $warehouseId,
                        abs($difference),
                        $movementType,
                        'stock_adjustment',
                        $countId,
                        $companyId,
                        $userId,
                        $unitCost,
                        "Inventory Adjustment for Count #{$countNo}"
                    );
                }
            }

            $this->countRepo->bulkInsertItems($countId, $processedItems);

            return $countId;
        });
    }
}