<?php
// Path: app/Core/Api/Filter.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\Request;

/**
 * Enterprise API Filter Helper
 * يستخرج وينظف الفلاتر القادمة من الـ API (مثال: ?filter[status]=active&filter[category_id]=5).
 */
class Filter
{
    /**
     * استخراج الفلاتر من الطلب وتنقيتها بناءً على الحقول المسموحة.
     *
     * @param Request $request
     * @param array $allowedFilters قائمة بأسماء الحقول المسموح الفلترة بها
     * @return array
     */
    public static function extract(Request $request, array $allowedFilters = []): array
    {
        $filters = $request->input('filter', []);

        if (!is_array($filters)) {
            return [];
        }

        $cleanFilters = [];

        foreach ($filters as $key => $value) {
            // تجاهل الحقول غير المسموحة إذا تم تحديد قائمة
            if (!empty($allowedFilters) && !in_array($key, $allowedFilters, true)) {
                continue;
            }

            // تجاهل القيم الفارغة
            if ($value === '' || $value === null) {
                continue;
            }

            $cleanFilters[$key] = $value;
        }

        return $cleanFilters;
    }
}