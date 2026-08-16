<?php
// Path: app/Modules/FixedAssets/Listeners/CreateDepreciationJournalEntry.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Core\Finance\Services\AccountingService;
use App\Modules\FixedAssets\Depreciation\Domain\Events\AssetDepreciatedEvent;

/**
 * Enterprise Listener: Create Depreciation Journal Entry
 * يراقب حدث إهلاك الأصل، ويقوم بتوليد القيد المحاسبي (مصروف الإهلاك مدين، ومجمع الإهلاك دائن) آلياً.
 */
class CreateDepreciationJournalEntry implements EventListener
{
    protected DatabaseManager $db;
    protected AccountingService $accounting;

    public function __construct(DatabaseManager $db, AccountingService $accounting)
    {
        $this->db = $db;
        $this->accounting = $accounting;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof AssetDepreciatedEvent) {
            return;
        }

        // جلب بيانات الحسابات المربوطة بالأصل
        $asset = $this->db->connection()->selectOne(
            "SELECT asset_code, accumulated_depreciation_account_id, depreciation_expense_account_id 
             FROM fixed_assets WHERE id = ?",
            [$event->assetId]
        );

        if (!$asset) return;

        $amount = $event->depreciationAmount;

        $header = [
            'entry_date'     => date('Y-m-t'), // آخر يوم في الشهر
            'description'    => "Monthly Depreciation for Asset [{$asset['asset_code']}]",
            'reference_type' => 'asset_depreciation',
            'reference_id'   => $event->recordId,
        ];

        $lines = [
            // المدين: مصروف الإهلاك
            ['account_id' => $asset['depreciation_expense_account_id'], 'debit' => $amount, 'credit' => 0.0],
            // الدائن: مجمع الإهلاك (يقلل من صافي القيمة الدفترية للأصل)
            ['account_id' => $asset['accumulated_depreciation_account_id'], 'debit' => 0.0, 'credit' => $amount]
        ];

        // ترحيل القيد
        $journalEntryId = $this->accounting->createJournalEntry($header, $lines, 0); // 0 = System Auto

        // ربط القيد بسجل الإهلاك
        $this->db->connection()->update(
            "UPDATE asset_depreciations SET journal_entry_id = ? WHERE id = ?",
            [$journalEntryId, $event->recordId]
        );
    }
}