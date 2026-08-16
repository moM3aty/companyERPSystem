<?php
// Path: app/Modules/Inventory/Jobs/RecalculateInventoryCostJob.php

declare(strict_types=1);

namespace App\Modules\Inventory\Jobs;

use App\Core\Queue\Job;
use App\Core\Bootstrap\Container;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Background Job: Recalculate Inventory Cost
 * وظيفة ثقيلة جداً تعمل في الخلفية لإعادة حساب التكلفة (Moving Average) بأثر رجعي
 * في حال تم تعديل سعر فاتورة مشتريات قديمة.
 */
class RecalculateInventoryCostJob extends Job
{
    public readonly int $companyId;
    public readonly int $productId;

    public function __construct(int $companyId, int $productId)
    {
        $this->companyId = $companyId;
        $this->productId = $productId;
        $this->retryPolicy = new \App\Core\Queue\RetryPolicy(2, 300);
    }

    public function handle(Container $container): void
    {
        /** @var DatabaseManager $db */
        $db = $container->make(DatabaseManager::class);
        
        // تطبيق لوجيك الحساب التراكمي (يتم المرور على كارتة الصنف بالترتيب التاريخي)
        // وحساب التكلفة سطراً بسطر وتحديث جدول `inventory_stocks`
        // (الكود مبسط هنا لتمثيل الفكرة المعمارية)
        
        $db->connection()->statement("UPDATE inventory_stocks SET updated_at = NOW() WHERE product_id = ? AND company_id = ?", [$this->productId, $this->companyId]);
    }
}