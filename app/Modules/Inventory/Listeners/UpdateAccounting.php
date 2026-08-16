<?php
// Path: app/Modules/Inventory/Listeners/UpdateAccounting.php

declare(strict_types=1);

namespace App\Modules\Inventory\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\CompanySettings;
use App\Modules\Inventory\Events\StockAdjusted;

/**
 * Enterprise Listener: Update Accounting from Inventory
 * يراقب التعديلات المخزنية (مثل التسوية) ويقوم بتوليد القيد المحاسبي تلقائياً لضبط حساب المخزون وحساب العجز/الزيادة.
 */
class UpdateAccounting implements EventListener
{
    protected AccountingService $accountingEngine;
    protected CompanySettings $settings;

    public function __construct(AccountingService $accountingEngine, CompanySettings $settings)
    {
        $this->accountingEngine = $accountingEngine;
        $this->settings = $settings;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof StockAdjusted) {
            return;
        }

        $inventoryAccount = (int) $this->settings->get('accounting.inventory_asset_id');
        $adjustmentAccount = (int) $this->settings->get('accounting.inventory_adjustment_id');

        if (!$inventoryAccount || !$adjustmentAccount) {
            return; // عدم وجود إعدادات يمنع الترحيل الآلي لتجنب الأخطاء
        }

        $amount = (float) $event->totalDifferenceValue;

        if ($amount == 0.0) return;

        $header = [
            'entry_date'     => date('Y-m-d'),
            'description'    => "Inventory Adjustment ID: {$event->entityId}",
            'reference_type' => 'inventory_adjustment',
            'reference_id'   => $event->entityId,
        ];

        $lines = [];

        if ($amount > 0) {
            // زيادة في المخزون (أصل يزيد)
            $lines[] = ['account_id' => $inventoryAccount, 'debit' => $amount, 'credit' => 0.0];
            $lines[] = ['account_id' => $adjustmentAccount, 'debit' => 0.0, 'credit' => $amount];
        } else {
            // عجز في المخزون (أصل ينقص)
            $absAmount = abs($amount);
            $lines[] = ['account_id' => $adjustmentAccount, 'debit' => $absAmount, 'credit' => 0.0];
            $lines[] = ['account_id' => $inventoryAccount, 'debit' => 0.0, 'credit' => $absAmount];
        }

        $this->accountingEngine->createJournalEntry($header, $lines, 0); // 0 = System
    }
}