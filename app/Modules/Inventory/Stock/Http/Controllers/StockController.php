<?php
// Path: app/Modules/Inventory/Stock/Http/Controllers/StockController.php

declare(strict_types=1);

namespace App\Modules\Inventory\Stock\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Inventory\Stock\Domain\StockRepositoryInterface;

/**
 * Enterprise API Controller: Stock
 * عرض الأرصدة الحالية للمستودعات بأسلوب للقراءة فقط (Read-Only).
 */
class StockController extends Controller
{
    protected StockRepositoryInterface $stockRepo;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(StockRepositoryInterface $stockRepo, Gate $gate, TenantContext $tenant)
    {
        $this->stockRepo = $stockRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function show(int $productId, int $warehouseId): JsonResponse
    {
        $this->gate->authorize('inventory', 'stock', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $stock = $this->stockRepo->findByProductAndWarehouse($productId, $warehouseId, $companyId);

        if (!$stock) {
            // نرجع استجابة صحيحة بصفر بدلاً من 404 لأن الصنف قد يكون جديداً ولم تدخله بضاعة بعد
            return ApiResponse::success([
                'product_id'        => $productId,
                'warehouse_id'      => $warehouseId,
                'quantity'          => 0.0,
                'reserved_quantity' => 0.0,
                'available'         => 0.0
            ]);
        }

        $data = $stock->toArray();
        $data['available'] = $stock->getAvailableQuantity();

        return ApiResponse::success($data);
    }
}