<?php
// Path: app/Modules/Assets/Services/AcquisitionService.php

declare(strict_types=1);

namespace App\Modules\Assets\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Finance\Services\AccountingService;
use App\Modules\FixedAssets\Assets\Application\AssetService;

class AcquisitionService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected AccountingService $accounting;
    protected AssetService $assetService;

    public function __construct(
        DatabaseManager $db, 
        TransactionManager $transaction, 
        AccountingService $accounting,
        AssetService $assetService
    ) {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->accounting = $accounting;
        $this->assetService = $assetService;
    }

    public function acquireAsset(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            // 1. إنشاء سجل الأصل الثابت
            $asset = $this->assetService->createAsset($data['asset_details'], $companyId);
            $assetId = (int) $asset->id;
            $cost = (float) $data['asset_details']['purchase_value'];

            // 2. إنشاء قيد الرسملة (Capitalization Entry)
            $jeHeader = [
                'entry_date'     => $data['acquisition_date'],
                'description'    => "Asset Capitalization: {$data['asset_details']['name']}",
                'reference_type' => 'asset_acquisition'
            ];

            $jeLines = [
                ['account_id' => $data['asset_details']['asset_account_id'], 'debit' => $cost, 'credit' => 0.0],
                ['account_id' => $data['clearing_account_id'], 'debit' => 0.0, 'credit' => $cost],
            ];

            $jeId = $this->accounting->createJournalEntry($jeHeader, $jeLines, $userId);

            // 3. حفظ وثيقة الاستحواذ
            $this->db->connection()->insert(
                "INSERT INTO asset_acquisitions (company_id, asset_id, supplier_id, invoice_number, acquisition_cost, acquisition_date, journal_entry_id, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$companyId, $assetId, $data['supplier_id'], $data['invoice_number'] ?? null, $cost, $data['acquisition_date'], $jeId, date('Y-m-d H:i:s')]
            );
            
            return (int) $this->db->connection()->lastInsertId();
        });
    }
}