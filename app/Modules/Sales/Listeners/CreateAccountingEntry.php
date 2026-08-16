<?php
// Path: app/Modules/Sales/Listeners/CreateAccountingEntry.php

declare(strict_types=1);

namespace App\Modules\Sales\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\CompanySettings;
use App\Core\Exceptions\BusinessException;
use App\Modules\Sales\Invoices\Domain\Events\InvoicePostedEvent;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Listener: Create Sales Accounting Entry
 * يتنصت على اعتماد فاتورة المبيعات ويقوم برمي القيد المحاسبي آلياً في دفتر الأستاذ العام.
 */
class CreateAccountingEntry implements EventListener
{
    protected AccountingService $accounting;
    protected CompanySettings $settings;
    protected DatabaseManager $db;

    public function __construct(AccountingService $accounting, CompanySettings $settings, DatabaseManager $db)
    {
        $this->accounting = $accounting;
        $this->settings = $settings;
        $this->db = $db;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof InvoicePostedEvent) {
            return;
        }

        $invoiceId = (int) $event->entityId;
        
        // جلب الفاتورة من الداتا بيز للحصول على رقمها والضرائب
        $invoice = $this->db->connection()->selectOne("SELECT invoice_no, subtotal, tax_total, grand_total, created_by FROM sales_invoices WHERE id = ?", [$invoiceId]);

        if (!$invoice) return;

        // جلب حسابات الربط من الإعدادات
        $accountingConfig = [
            'ar_account_id'    => (int) $this->settings->get('accounting.accounts_receivable_id'),
            'sales_account_id' => (int) $this->settings->get('accounting.sales_revenue_id'),
            'tax_account_id'   => (int) $this->settings->get('accounting.tax_payable_id'),
        ];

        // إرسال الطلب للمحرك المالي
        $this->accounting->postSalesInvoice(
            $invoiceId,
            $invoice['invoice_no'],
            (float) $invoice['grand_total'],
            (float) $invoice['subtotal'],
            (float) $invoice['tax_total'],
            $accountingConfig,
            (int) $invoice['created_by']
        );
    }
}