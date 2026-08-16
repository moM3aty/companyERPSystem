<?php
// Path: app/Core/Integrations/MappingManager.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Database\DatabaseManager;
use App\Core\Helpers\Arr;

/**
 * Enterprise Mapping Manager
 * يحل مشكلة اختلاف مسميات الحقول بين نظامك والأنظمة الأخرى.
 * (مثال: حقل السعر في نظامك "price"، وفي Shopify اسمه "variants.0.price").
 */
class MappingManager
{
    protected DatabaseManager $db;


    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تحويل كائن محلي إلى مصفوفة خارجية بناءً على خريطة (Map) معرفة في قاعدة البيانات.
     *
     * @param array $localData البيانات من نظامك
     * @param int $integrationId معرف الربط لاستدعاء الخريطة
     * @param string $entityType نوع الكيان (مثال: 'product')
     * @return array البيانات بتنسيق النظام الخارجي
     */
    public function mapToExternal(array $localData, int $integrationId, string $entityType): array
    {
        // جلب خريطة الحقول من الداتابيز (مثال: {"price": "variants.0.price", "product_name": "title"})
        $sql = "SELECT local_field, external_field FROM integration_mappings 
                WHERE integration_id = ? AND entity_type = ?";
        
        $mappings = $this->db->connection()->select($sql, [$integrationId, $entityType]);

        if (empty($mappings)) {
            return $localData; // لا يوجد خريطة، نرسل البيانات كما هي
        }

        $externalData = [];

        foreach ($mappings as $map) {
            $localKey = $map['local_field'];
            $externalKey = $map['external_field'];

            // استخدام دالة Get لدعم الـ Dot Notation (جلب القيم المتداخلة)
            $value = Arr::get($localData, $localKey);

            // استخدام دالة Set لدعم الـ Dot Notation (بناء مصفوفة متداخلة)
            Arr::set($externalData, $externalKey, $value);
        }

        return $externalData;
    }
}