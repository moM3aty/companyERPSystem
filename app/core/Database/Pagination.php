<?php
// Path: app/Core/Database/Pagination.php

declare(strict_types=1);

namespace App\Core\Database;

use JsonSerializable;

/**
 * Enterprise Pagination DTO
 * يغلف البيانات المقسمة لصفحات ويوفر واجهة موحدة للتصدير كـ JSON.
 * ضروري جداً لتعامل تطبيقات الويب والـ DataTables مع الجداول الضخمة.
 */
class Pagination implements JsonSerializable
{
    public readonly array $items;
    public readonly int $total;
    public readonly int $perPage;
    public readonly int $currentPage;
    public readonly int $lastPage;

    /**
     * Pagination constructor.
     *
     * @param array $items البيانات الفعلية لهذه الصفحة
     * @param int $total إجمالي عدد السجلات في قاعدة البيانات
     * @param int $perPage عدد السجلات في الصفحة الواحدة
     * @param int $currentPage الصفحة الحالية
     */
    public function __construct(array $items, int $total, int $perPage, int $currentPage)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
        
        $this->lastPage = (int) ceil($this->total / $this->perPage);
    }

    /**
     * التحقق من وجود صفحة تالية.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * التحقق من وجود صفحة سابقة.
     *
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * تحويل الكائن لمصفوفة.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->items,
            'meta' => [
                'total'        => $this->total,
                'per_page'     => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page'    => $this->lastPage,
                'has_more'     => $this->hasMorePages(),
            ]
        ];
    }

    /**
     * دعم دالة json_encode بشكل أصلي.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}