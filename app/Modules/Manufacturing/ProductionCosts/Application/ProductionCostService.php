<?php
// Path: app/Modules/Manufacturing/ProductionCosts/Application/ProductionCostService.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionCosts\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Production Costing Engine
 * يحسب التكلفة المعيارية (Standard Cost) للمنتج من خلال جمع تكاليف المواد (BOM) وتكاليف التشغيل (Routing).
 */
class ProductionCostService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * احتساب تكلفة تصنيع الوحدة الواحدة للمنتج.
     *
     * @param int $productId
     * @param int $companyId
     * @return array
     * @throws BusinessException
     */
    public function calculateEstimatedCost(int $productId, int $companyId): array
    {
        // 1. حساب تكلفة المواد الخام (Material Costs) بناءً على הـ BOM النشط
        $materialsSql = "
            SELECT bi.quantity, bi.scrap_percentage, s.average_cost, p.default_price
            FROM manufacturing_boms b
            JOIN manufacturing_bom_items bi ON b.id = bi.bom_id
            JOIN products p ON bi.component_product_id = p.id
            LEFT JOIN inventory_stocks s ON p.id = s.product_id
            WHERE b.product_id = ? AND b.company_id = ? AND b.is_active = 1
        ";
        
        $bomItems = $this->db->connection()->select($materialsSql, [$productId, $companyId]);
        
        $totalMaterialCost = 0.0;
        foreach ($bomItems as $item) {
            $costPerUnit = (float) ($item['average_cost'] > 0 ? $item['average_cost'] : $item['default_price']);
            $qtyNeeded = (float) $item['quantity'];
            $scrapMultiplier = 1 + ((float) $item['scrap_percentage'] / 100);
            
            $totalMaterialCost += ($qtyNeeded * $scrapMultiplier) * $costPerUnit;
        }

        // 2. حساب تكلفة العمليات/التشغيل (Operational Costs) بناءً على הـ Routing النشط
        $routingSql = "
            SELECT rs.setup_time_minutes, rs.execution_time_minutes, wc.cost_per_hour
            FROM manufacturing_routings r
            JOIN manufacturing_routing_steps rs ON r.id = rs.routing_id
            JOIN manufacturing_work_centers wc ON rs.work_center_id = wc.id
            WHERE r.product_id = ? AND r.company_id = ? AND r.is_active = 1
        ";

        $routingSteps = $this->db->connection()->select($routingSql, [$productId, $companyId]);
        
        $totalOperationalCost = 0.0;
        foreach ($routingSteps as $step) {
            // تحويل الدقائق لساعات لحساب التكلفة
            $executionHours = (float) $step['execution_time_minutes'] / 60;
            $setupHours = (float) $step['setup_time_minutes'] / 60; // يوزع على الـ Batch عادة، نفترضه للوحدة للتبسيط
            
            $costPerHour = (float) $step['cost_per_hour'];
            $totalOperationalCost += ($executionHours + $setupHours) * $costPerHour;
        }

        $grandTotal = $totalMaterialCost + $totalOperationalCost;

        return [
            'product_id'             => $productId,
            'material_cost'          => round($totalMaterialCost, 2),
            'operational_cost'       => round($totalOperationalCost, 2),
            'estimated_unit_cost'    => round($grandTotal, 2),
            'calculation_timestamp'  => date('Y-m-d H:i:s')
        ];
    }
}