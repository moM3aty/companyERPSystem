<?php
// Path: app/Modules/FixedAssets/Disposal/Application/DisposalService.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Disposal\Application;

use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\CompanySettings;
use App\Core\Exceptions\BusinessException;
use App\Modules\FixedAssets\Disposal\Infrastructure\AssetDisposalRepository;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Asset Disposal Engine
 * المحرك المالي الأعقد في الأصول. يقوم بتصفير الأصل، وتسجيل أرباح/خسائر البيع آلياً في دفتر الأستاذ.
 */
class DisposalService
{
    protected AssetDisposalRepository $disposalRepo;
    protected AccountingService $accounting;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected CompanySettings $settings;
    protected TenantContext $tenant;

    public function __construct(
        AssetDisposalRepository $disposalRepo,
        AccountingService $accounting,
        TransactionManager $transaction,
        DatabaseManager $db,
        CompanySettings $settings,
        TenantContext $tenant
    ) {
        $this->disposalRepo = $disposalRepo;
        $this->accounting = $accounting;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->settings = $settings;
        $this->tenant = $tenant;
    }

    public function disposeAsset(array $data, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $assetId = (int) $data['asset_id'];
            
            // 1. جلب بيانات الأصل وحبسه مؤقتاً
            $asset = $this->db->connection()->selectOne("SELECT * FROM fixed_assets WHERE id = ? AND company_id = ? FOR UPDATE", [$assetId, $companyId]);

            if (!$asset || $asset['status'] !== 'active') {
                throw new BusinessException("Asset is not active or already disposed.");
            }

            $purchaseValue = (float) $asset['purchase_value'];
            $accumulatedDepr = (float) $asset['accumulated_depreciation'];
            $netBookValue = (float) $asset['net_book_value'];
            $saleAmount = $data['disposal_type'] === 'sold' ? (float) $data['sale_amount'] : 0.0;
            
            // 2. حساب الأرباح أو الخسائر الرأسمالية
            $gainLoss = round($saleAmount - $netBookValue, 2);

            // 3. بناء القيد المحاسبي للإقفال
            $jeHeader = [
                'entry_date'     => $data['disposal_date'],
                'description'    => "Asset Disposal: {$asset['asset_code']} - {$asset['name']}",
                'reference_type' => 'asset_disposal',
            ];

            $jeLines = [];

            // أ. إغلاق حساب الأصل (Credit)
            $jeLines[] = ['account_id' => $asset['asset_account_id'], 'debit' => 0.0, 'credit' => $purchaseValue];

            // ب. إغلاق مجمع الإهلاك (Debit)
            if ($accumulatedDepr > 0) {
                $jeLines[] = ['account_id' => $asset['accumulated_depreciation_account_id'], 'debit' => $accumulatedDepr, 'credit' => 0.0];
            }

            // ج. إثبات النقدية أو الذمم إذا تم البيع (Debit)
            if ($saleAmount > 0) {
                $jeLines[] = ['account_id' => $data['debit_account_id'], 'debit' => $saleAmount, 'credit' => 0.0];
            }

            // د. تسجيل الأرباح أو الخسائر
            if ($gainLoss > 0) {
                $gainAccountId = (int) $this->settings->get('accounting.gain_on_disposal_id');
                if (!$gainAccountId) throw new BusinessException("Gain on disposal account is missing in settings.");
                $jeLines[] = ['account_id' => $gainAccountId, 'debit' => 0.0, 'credit' => $gainLoss];
            } elseif ($gainLoss < 0) {
                $lossAccountId = (int) $this->settings->get('accounting.loss_on_disposal_id');
                if (!$lossAccountId) throw new BusinessException("Loss on disposal account is missing in settings.");
                $jeLines[] = ['account_id' => $lossAccountId, 'debit' => abs($gainLoss), 'credit' => 0.0];
            }

            $journalEntryId = $this->accounting->createJournalEntry($jeHeader, $jeLines, $userId);

            // 4. تحديث حالة الأصل في قاعدة البيانات
            $this->db->connection()->update(
                "UPDATE fixed_assets SET status = ?, updated_at = ? WHERE id = ?",
                [$data['disposal_type'] === 'sold' ? 'sold' : 'disposed', date('Y-m-d H:i:s'), $assetId]
            );

            // 5. حفظ سجل الاستبعاد
            $disposalData = [
                'company_id'       => $companyId,
                'asset_id'         => $assetId,
                'disposal_date'    => $data['disposal_date'],
                'disposal_type'    => $data['disposal_type'],
                'sale_amount'      => $saleAmount,
                'net_book_value'   => $netBookValue,
                'gain_loss_amount' => $gainLoss,
                'journal_entry_id' => $journalEntryId,
                'reason'           => $data['reason'] ?? '',
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            return $this->disposalRepo->create($disposalData);
        });
    }
}