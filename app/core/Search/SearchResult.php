<?php
// Path: app/Core/Search/SearchResult.php

declare(strict_types=1);

namespace App\Core\Search;

use JsonSerializable;

/**
 * Enterprise Search Result DTO
 * كائن يغلف نتائج البحث لتكون موحدة بغض النظر عن محرك البحث المستخدم (DB أو Elastic).
 */
class SearchResult implements JsonSerializable
{
    public readonly array $items;
    public readonly int $totalCount;
    public readonly float $executionTimeMs;

    /**
     * SearchResult constructor.
     *
     * @param array $items البيانات المطابقة
     * @param int $totalCount إجمالي النتائج (لتسهيل الـ Pagination)
     * @param float $executionTimeMs وقت تنفيذ البحث بالمللي ثانية
     */
    public function __construct(array $items, int $totalCount, float $executionTimeMs = 0.0)
    {
        $this->items = $items;
        $this->totalCount = $totalCount;
        $this->executionTimeMs = $executionTimeMs;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'data'  => $this->items,
            'meta'  => [
                'total_results'  => $this->totalCount,
                'execution_time' => $this->executionTimeMs . ' ms',
            ]
        ];
    }
}