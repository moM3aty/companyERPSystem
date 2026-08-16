<?php
// Path: app/Modules/Sales/Subscription/Application/SubscriptionService.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Application;

use App\Modules\Sales\Subscription\Domain\SubscriptionRepositoryInterface;
use App\Core\Sales\Services\InvoiceService;
use App\Core\Database\TransactionManager;
use App\Core\Contracts\LoggerInterface;
use App\Core\Tenant\TenantContext;
use App\Core\Tenant\Tenant;

/**
 * Enterprise Application Service: Subscriptions
 * يقوم بتوليد فواتير المبيعات (Sales Invoices) للمشتركين دورياً بشكل تلقائي.
 */
class SubscriptionService
{
    protected SubscriptionRepositoryInterface $repo;
    protected InvoiceService $invoiceService;
    protected TransactionManager $transaction;
    protected LoggerInterface $logger;
    protected TenantContext $tenantContext;

    public function __construct(
        SubscriptionRepositoryInterface $repo,
        InvoiceService $invoiceService,
        TransactionManager $transaction,
        LoggerInterface $logger,
        TenantContext $tenantContext
    ) {
        $this->repo = $repo;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->logger = $logger;
        $this->tenantContext = $tenantContext;
    }

    public function setupSubscription(array $data, int $companyId, int $userId): int
    {
        $data['company_id'] = $companyId;
        $data['status']     = 'active';
        $data['created_by'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->repo->create($data);
    }

    /**
     * معالجة كافة الاشتراكات المستحقة (تُستدعى يومياً عبر הـ Cron).
     */
    public function processDueSubscriptions(): void
    {
        $today = date('Y-m-d');
        $this->repo->setTenantId(0); 
        
        $subscriptions = $this->repo->getDueSubscriptions($today);

        foreach ($subscriptions as $sub) {
            try {
                $this->transaction->execute(function () use ($sub, $today) {
                    $companyId = (int) $sub['company_id'];
                    
                    // حقن سياق الشركة حتى يحسب محرك الفواتير المجاميع بشكل سليم
                    $this->tenantContext->setTenant(new Tenant($companyId));

                    $headerData = [
                        'customer_id'  => $sub['customer_id'],
                        'invoice_date' => $today,
                        'due_date'     => $today,
                        'currency_id'  => $sub['currency_id'],
                    ];

                    $itemsData = [
                        [
                            'product_id' => $sub['product_id'],
                            'description' => "Subscription Billing: " . ucfirst($sub['billing_cycle']),
                            'quantity'   => 1,
                            'unit_price' => $sub['price'],
                        ]
                    ];

                    // 1. توليد فاتورة المبيعات آلياً (هنا InvoiceService يقوم بالضرائب والتفاصيل)
                    $this->invoiceService->createInvoice($headerData, $itemsData, 0);

                    // 2. تحديث تاريخ الفاتورة القادمة
                    $nextRunDate = $this->calculateNextBillingDate($today, $sub['billing_cycle']);
                    
                    $updateData = ['next_billing_date' => $nextRunDate, 'updated_at' => date('Y-m-d H:i:s')];

                    if (!empty($sub['end_date']) && $nextRunDate > $sub['end_date']) {
                        $updateData['status'] = 'completed';
                    }

                    $this->repo->update((int) $sub['id'], $updateData);
                });

                $this->logger->info("Successfully processed Subscription ID [{$sub['id']}]. Invoice Generated.");
            } catch (\Throwable $e) {
                $this->logger->error("Failed to process Subscription ID [{$sub['id']}]: " . $e->getMessage());
            }
        }
    }

    protected function calculateNextBillingDate(string $currentDate, string $cycle): string
    {
        $date = new \DateTime($currentDate);
        return match ($cycle) {
            'monthly'   => $date->modify('+1 month')->format('Y-m-d'),
            'quarterly' => $date->modify('+3 months')->format('Y-m-d'),
            'yearly'    => $date->modify('+1 year')->format('Y-m-d'),
            default     => $date->modify('+1 month')->format('Y-m-d'),
        };
    }
}