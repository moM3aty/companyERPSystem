<?php
// Path: app/Modules/FixedAssets/Depreciation/Application/DepreciationService.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Application;

use App\Modules\FixedAssets\Assets\Domain\AssetRepositoryInterface;
use App\Modules\FixedAssets\Depreciation\Domain\DepreciationRepositoryInterface;
use App\Modules\FixedAssets\Depreciation\Domain\Events\AssetDepreciatedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Depreciation Engine
 * يقوم بحساب قسط الإهلاك (طريقة القسط الثابت - Straight Line) وتسجيله أمنياً.
 */
class DepreciationService
{
    protected AssetRepositoryInterface $assetRepo;
    protected DepreciationRepositoryInterface $depreciationRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        AssetRepositoryInterface $assetRepo,
        DepreciationRepositoryInterface $depreciationRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->assetRepo = $assetRepo;
        $this->depreciationRepo = $depreciationRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    /**
     * تشغيل قسط إهلاك شهري لأصل معين.
     *
     * @param int $assetId
     * @param int $year
     * @param int $month
     * @param int $companyId
     * @return void
     * @throws BusinessException|\Throwable
     */
    public function runMonthlyDepreciation(int $assetId, int $year, int $month, int $companyId): void
    {
        $this->transaction->execute(function () use ($assetId, $year, $month, $companyId) {
            
            // 1. التحقق من عدم إهلاك الأصل مسبقاً في هذا الشهر
            if ($this->depreciationRepo->isDepreciatedForPeriod($assetId, $year, $month)) {
                throw new BusinessException("Asset [ID: {$assetId}] has already been depreciated for {$month}/{$year}.");
            }

            // 2. جلب بيانات الأصل
            $this->assetRepo->setTenantId($companyId);
            $assetData = $this->assetRepo->findOrFail($assetId);
            
            $status = $assetData['status'];
            $purchaseValue = (float) $assetData['purchase_value'];
            $salvageValue = (float) $assetData['salvage_value'];
            $usefulLife = (int) $assetData['useful_life_months'];
            $accumulated = (float) $assetData['accumulated_depreciation'];
            $netBookValue = (float) $assetData['net_book_value'];

            // 3. التحقق من قابلية الأصل للإهلاك
            if ($status !== 'active') {
                throw new BusinessException("Cannot depreciate an inactive asset.");
            }

            if ($netBookValue <= $salvageValue) {
                // تم إهلاك الأصل بالكامل
                return; 
            }

            // 4. الحساب (Straight Line Method)
            $depreciableBase = $purchaseValue - $salvageValue;
            $monthlyExpense = round($depreciableBase / $usefulLife, 2);

            // التأكد من أن القسط لا يهبط بالقيمة الدفترية تحت قيمة الخردة (Salvage)
            if (($netBookValue - $monthlyExpense) < $salvageValue) {
                $monthlyExpense = $netBookValue - $salvageValue;
            }

            if ($monthlyExpense <= 0) {
                return; // لا يوجد إهلاك متبقي
            }

            // 5. تحديث أرصدة الأصل
            $newAccumulated = $accumulated + $monthlyExpense;
            $newNetBookValue = $purchaseValue - $newAccumulated;

            $this->assetRepo->update($assetId, [
                'accumulated_depreciation' => $newAccumulated,
                'net_book_value'           => $newNetBookValue,
            ]);

            // 6. تسجيل حركة الإهلاك
            $recordId = $this->depreciationRepo->create([
                'company_id'          => $companyId,
                'asset_id'            => $assetId,
                'period_year'         => $year,
                'period_month'        => $month,
                'depreciation_amount' => $monthlyExpense,
                'created_at'          => date('Y-m-d H:i:s')
            ]);

            // 7. إطلاق الحدث للنظام المحاسبي لتوليد القيود
            $this->eventBus->publish(new AssetDepreciatedEvent($recordId, $companyId, $assetId, $monthlyExpense));
        });
    }
}