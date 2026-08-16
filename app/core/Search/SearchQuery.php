<?php
// Path: app/Core/Search/SearchQuery.php

declare(strict_types=1);

namespace App\Core\Search;

/**
 * Enterprise Search Query DTO
 * كائن يغلف معايير البحث (النص، الفلاتر، التقسيم) لضمان توحيد الإدخال لمحركات البحث المختلفة.
 */
class SearchQuery
{
    public readonly string $index;
    public readonly string $term;
    public readonly array $filters;
    public readonly int $limit;
    public readonly int $offset;
    public readonly array $sort;

    /**
     * SearchQuery constructor.
     *
     * @param string $index اسم الجدول أو الكيان (مثال: 'customers')
     * @param string $term النص المراد البحث عنه
     * @param array $filters فلاتر إضافية (مثال: ['status' => 'active'])
     * @param int $limit
     * @param int $offset
     * @param array $sort ترتيب (مثال: ['created_at' => 'desc'])
     */
    public function __construct(
        string $index,
        string $term = '',
        array $filters = [],
        int $limit = 20,
        int $offset = 0,
        array $sort = []
    ) {
        $this->index = $index;
        $this->term = trim($term);
        $this->filters = $filters;
        $this->limit = max(1, $limit);
        $this->offset = max(0, $offset);
        $this->sort = $sort;
    }

    /**
     * التحقق مما إذا كان هناك نص بحث مُدخل.
     *
     * @return bool
     */
    public function hasTerm(): bool
    {
        return $this->term !== '';
    }
}