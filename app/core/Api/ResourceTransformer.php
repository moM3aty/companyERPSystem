<?php
// Path: app/Core/Api/ResourceTransformer.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Database\Pagination;

/**
 * Enterprise API Resource Transformer (Abstract)
 * طبقة وسيطة (Presentation Layer) لتحويل البيانات الخام من قاعدة البيانات (Arrays أو Models) 
 * إلى شكل نهائي ومنظم ومحمي لتطبيقات الـ Frontend والموبايل. يمنع تسريب تفاصيل الهيكلة الداخلية.
 */
abstract class ResourceTransformer
{
    /**
     * الدالة الرئيسية التي يجب على كل محول (Transformer) تنفيذها.
     * تحدد بالضبط الحقول التي سيتم إرجاعها للـ API.
     *
     * @param mixed $resource (Array or Entity)
     * @return array
     */
    abstract public function transform(mixed $resource): array;

    /**
     * تحويل مصفوفة من السجلات (Collection).
     *
     * @param array $resources
     * @return array
     */
    public function transformCollection(array $resources): array
    {
        return array_map([$this, 'transform'], $resources);
    }

    /**
     * تحويل كائن Pagination بشكل مباشر.
     * يحتفظ ببيانات الـ Meta للـ Pagination ويقوم بتحويل البيانات داخل الـ Data.
     *
     * @param Pagination $pagination
     * @return Pagination
     */
    public function transformPagination(Pagination $pagination): Pagination
    {
        $transformedItems = $this->transformCollection($pagination->items);

        return new Pagination(
            $transformedItems,
            $pagination->total,
            $pagination->perPage,
            $pagination->currentPage
        );
    }

    /**
     * Helper Function: آمن لجلب القيم لتجنب الأخطاء إذا كانت القيمة غير موجودة.
     *
     * @param mixed $resource
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(mixed $resource, string $key, mixed $default = null): mixed
    {
        if (is_array($resource)) {
            return array_key_exists($key, $resource) ? $resource[$key] : $default;
        }

        if (is_object($resource) && isset($resource->{$key})) {
            return $resource->{$key};
        }

        return $default;
    }
}