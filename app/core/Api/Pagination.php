<?php
// Path: app/Core/Api/Pagination.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\Request;

/**
 * Enterprise API Pagination Helper
 * يساعد في استخراج بيانات التقسيم (Pagination) من الطلب لتمريرها إلى الـ Repositories.
 */
class Pagination
{
    /**
     * استخراج بيانات التقسيم من الطلب.
     *
     * @param Request $request
     * @param int $defaultPerPage
     * @param int $maxPerPage
     * @return array ['page' => int, 'per_page' => int]
     */
    public static function extract(Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): array
    {
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', $defaultPerPage);

        // حماية من الأرقام السالبة
        $page = max(1, $page);
        
        // حماية من طلب آلاف السجلات في صفحة واحدة وإرهاق السيرفر
        $perPage = max(1, min($perPage, $maxPerPage));

        return [
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }
}