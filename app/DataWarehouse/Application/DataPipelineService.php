<?php
// Path: app/DataWarehouse/Application/DataPipelineService.php

declare(strict_types=1);

namespace App\DataWarehouse\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Data Warehouse: ETL Pipeline Service
 * يقوم بسحب البيانات من قاعدة البيانات التشغيلية (Operational DB)، تحويلها (Transform)، 
 * وضخها في جداول تحليلية (Data Marts/Fact Tables) مصممة لاستعلامات הـ BI السريعة.
 * يعمل عادة كـ Cron Job في أوقات خمول السيرفر.
 */
class DataPipelineService
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * تشغيل تدفق البيانات لجداول حقائق المبيعات (Sales Fact Table).
     *
     * @param string $date تاريخ التشغيل لسحب بيانات يوم واحد فقط (Incremental Load)
     * @return void
     */
    public function syncSalesFacts(string $date): void
    {
        $this->logger->info("Starting Data Pipeline: Syncing Sales Facts for {$date}");

        // 1. Extract: سحب المبيعات المرحلة في هذا اليوم
        $sql = "
            SELECT 
                si.company_id,
                si.branch_id,
                si.customer_id,
                sii.product_id,
                si.invoice_date as dim_date,
                sii.quantity,
                sii.total as net_revenue
            FROM sales_invoices si
            JOIN sales_invoice_items sii ON si.id = sii.invoice_id
            WHERE si.status = 'posted' AND si.invoice_date = ?
        ";

        $salesData = $this->db->connection()->select($sql, [$date]);

        if (empty($salesData)) {
            $this->logger->info("No sales data to sync for {$date}.");
            return;
        }

        // 2. Transform & Load: إدخال البيانات في جدول الـ DW المسطح (Fact Table)
        // يتم استخدام INSERT IGNORE أو REPLACE لمنع التكرار إذا عمل الـ Script مرتين
        
        $this->db->connection()->beginTransaction();
        try {
            foreach ($salesData as $row) {
                // جدول fact_sales مخصص للـ BI ولا يحتوي على أي علاقات (Foreign Keys) ثقيلة
                $this->db->connection()->insert(
                    "INSERT IGNORE INTO dw_fact_sales 
                     (company_id, branch_id, customer_id, product_id, dim_date, quantity_sold, net_revenue, sync_timestamp) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $row['company_id'],
                        $row['branch_id'],
                        $row['customer_id'],
                        $row['product_id'],
                        $row['dim_date'],
                        $row['quantity'],
                        $row['net_revenue'],
                        date('Y-m-d H:i:s')
                    ]
                );
            }
            $this->db->connection()->commit();
            $this->logger->info("Successfully synced " . count($salesData) . " sales fact records.");
        } catch (\Throwable $e) {
            $this->db->connection()->rollBack();
            $this->logger->error("Data Pipeline failed for Sales Facts: " . $e->getMessage());
        }
    }
}