<?php
// Path: app/Modules/Purchasing/Listeners/CreateAccountsPayableEntry.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\CompanySettings;
use App\Core\Exceptions\BusinessException;
use App\Modules\Purchasing\Events\PurchaseInvoicePosted;

/**
 * Enterprise Listener: Create Accounts Payable Entry
 * يستمع لحدث اعتماد فاتورة المشتريات ليقوم بإنشاء قيد دائن للمورد (AP) ومدين للمخزون أو المصروف.
 */
class CreateAccountsPayableEntry implements EventListener
{
    protected AccountingService $accounting;
    protected CompanySettings $settings;

    public function __construct(AccountingService $accounting, CompanySettings $settings)
    {
        $this->accounting = $accounting;
        $this->settings = $settings;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof PurchaseInvoicePosted) {
            return;
        }

        $invoiceId = (int) $event->entityId;
        
        // جلب حسابات الربط من الإعدادات
        $apAccountId = (int) $this->settings->get('accounting.accounts_payable_id');
        $inventoryAccrualAccountId = (int) $this->settings->get('accounting.inventory_accrual_id'); // حساب وسيط يتم إقفاله هنا
        $taxAccountId = (int) $this->settings->get('accounting.tax_receivable_id');

        if (!$apAccountId || !$inventoryAccrualAccountId) {
            throw new BusinessException("Cannot post Purchase Invoice. Accounts Payable or Accrual accounts are not configured.");
        }

        $header = [
            'entry_date'     => date('Y-m-d'),
            'description'    => "AP Recognition for Purchase Invoice #{$invoiceId}",
            'reference_type' => 'purchase_invoice',
            'reference_id'   => $invoiceId,
        ];

        $lines = [];
        $subtotal = $event->grandTotal - $event->taxTotal;

        // المدين: حساب التسويات المخزنية (لأنه زاد عند الاستلام في الـ GRN، ونقفله هنا)
        $lines[] = ['account_id' => $inventoryAccrualAccountId, 'debit' => $subtotal, 'credit' => 0.0];
        
        // المدين: ضريبة المدخلات (مستردة)
        if ($event->taxTotal > 0) {
            $lines[] = ['account_id' => $taxAccountId, 'debit' => $event->taxTotal, 'credit' => 0.0];
        }

        // الدائن: حساب ذمم الموردين (AP)
        $lines[] = ['account_id' => $apAccountId, 'debit' => 0.0, 'credit' => $event->grandTotal];

        $this->accounting->createJournalEntry($header, $lines, 0); // 0 = System
    }
}