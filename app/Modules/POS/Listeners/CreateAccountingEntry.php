<?php
// Path: app/Modules/POS/Listeners/CreateAccountingEntry.php

declare(strict_types=1);

namespace App\Modules\POS\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\BranchSettings;
use App\Modules\POS\Orders\Domain\Events\PosOrderCompletedEvent;

/**
 * Enterprise Listener: POS Create Accounting Entry
 * يرحل المبيعات النقدية لنقطة البيع إلى المحاسبة (مدين للصندوق، دائن للمبيعات).
 */
class CreateAccountingEntry implements EventListener
{
    protected DatabaseManager $db;
    protected AccountingService $accounting;
    protected BranchSettings $settings;

    public function __construct(DatabaseManager $db, AccountingService $accounting, BranchSettings $settings)
    {
        $this->db = $db;
        $this->accounting = $accounting;
        $this->settings = $settings;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof PosOrderCompletedEvent) {
            return;
        }

        $order = $this->db->connection()->selectOne("SELECT order_number, tax_total, subtotal, discount_total FROM pos_orders WHERE id = ?", [$event->entityId]);
        
        $cashAccountId = (int) $this->settings->get('pos.cash_account_id', 1);
        $salesAccountId = (int) $this->settings->get('accounting.sales_revenue_id', 2);

        $netSales = (float) $order['subtotal'] - (float) $order['discount_total'];

        $lines = [
            ['account_id' => $cashAccountId, 'debit' => $event->grandTotal, 'credit' => 0.0],
            ['account_id' => $salesAccountId, 'debit' => 0.0, 'credit' => $netSales],
        ];

        // إضافة الضريبة إن وجدت
        if ((float) $order['tax_total'] > 0) {
            $taxAccountId = (int) $this->settings->get('accounting.tax_payable_id', 3);
            $lines[] = ['account_id' => $taxAccountId, 'debit' => 0.0, 'credit' => (float) $order['tax_total']];
        }

        $header = [
            'entry_date'     => date('Y-m-d'),
            'description'    => "POS Sale #{$order['order_number']}",
            'reference_type' => 'pos_order',
            'reference_id'   => $event->entityId,
        ];

        $this->accounting->createJournalEntry($header, $lines, 0);
    }
}