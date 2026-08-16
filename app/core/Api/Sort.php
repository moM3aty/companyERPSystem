<?php
// Path: app/Core/Api/Sort.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\Request;

/**
 * Enterprise API Sort Helper
 * يستخرج إعدادات الترتيب من الطلب (مثال: ?sort=-created_at,name).
 */
class Sort
{
    /**
     * استخراج إعدادات الترتيب من الطلب وتنقيتها بناءً على الحقول المسموحة.
     *
     * @param Request $request
     * @param array $allowedSorts قائمة بأسماء الحقول المسموح الترتيب بها
     * @param string $defaultSort الترتيب الافتراضي (مثال: '-id')
     * @return array ['column' => 'direction']
     */
    public static function extract(Request $request, array $allowedSorts = [], string $defaultSort = '-id'): array
    {
        $sortString = $request->input('sort', $defaultSort);

        if (empty($sortString) || !is_string($sortString)) {
            return [];
        }

        $sorts = explode(',', $sortString);
        $cleanSorts = [];

        foreach ($sorts as $sort) {
            $sort = trim($sort);
            if (empty($sort)) continue;

            $direction = 'asc';
            $column = $sort;

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $column = substr($sort, 1);
            }

            // التأكد من أن الحقل مسموح الترتيب به
            if (!empty($allowedSorts) && !in_array($column, $allowedSorts, true)) {
                continue;
            }

            $cleanSorts[$column] = $direction;
        }

        return $cleanSorts;
    }
}