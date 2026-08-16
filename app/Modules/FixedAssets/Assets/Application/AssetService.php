<?php
// Path: app/Modules/FixedAssets/Assets/Application/AssetService.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Application;

use App\Modules\FixedAssets\Assets\Domain\Asset;
use App\Modules\FixedAssets\Assets\Domain\AssetRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Asset
 */
class AssetService
{
    protected AssetRepositoryInterface $assetRepo;
    protected TransactionManager $transaction;

    public function __construct(AssetRepositoryInterface $assetRepo, TransactionManager $transaction)
    {
        $this->assetRepo = $assetRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء أصل ثابت جديد.
     *
     * @param array $data
     * @param int $companyId
     * @return Asset
     * @throws BusinessException|\Throwable
     */
    public function createAsset(array $data, int $companyId): Asset
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['status'] = 'active';
            
            // القيمة الدفترية المبدئية تساوي قيمة الشراء
            $data['net_book_value'] = (float) $data['purchase_value'];
            $data['accumulated_depreciation'] = 0.00;
            $data['salvage_value'] = (float) ($data['salvage_value'] ?? 0.00);

            if ($data['salvage_value'] >= $data['purchase_value']) {
                throw new BusinessException("Salvage value cannot be greater than or equal to the purchase value.");
            }

            $assetId = $this->assetRepo->create($data);

            $this->assetRepo->setTenantId($companyId);
            $assetData = $this->assetRepo->findOrFail($assetId);

            return new Asset($assetData);
        });
    }
}