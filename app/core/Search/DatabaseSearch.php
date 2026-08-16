<?php
// Path: app/Core/Search/DatabaseSearch.php

declare(strict_types=1);

namespace App\Core\Search;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Database Search Provider
 * تطبيق لمحرك البحث يعتمد على استعلامات SQL (LIKE & WHERE) كحل افتراضي وقوي.
 */
class DatabaseSearch implements SearchProviderInterface
{
    protected DatabaseManager $db;
    
    /**
     * خريطة الحقول القابلة للبحث في كل جدول.
     *
     * @var array
     */
    protected array $searchableColumns = [
        'customers' => ['customer_code', 'company_name', 'email', 'phone'],
        'products'  => ['product_code', 'name', 'barcode'],
        'invoices'  => ['invoice_no', 'status'],
    ];

    /**
     * DatabaseSearch constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function search(SearchQuery $query): SearchResult
    {
        $startTime = microtime(true);
        $builder = $this->db->connection()->newQuery()->table($query->index);

        // 1. تطبيق الفلاتر الثابتة (Filters)
        foreach ($query->filters as $column => $value) {
            $builder->where($column, '=', $value);
        }

        // 2. تطبيق نص البحث (LIKE)
        if ($query->hasTerm() && isset($this->searchableColumns[$query->index])) {
            $term = '%' . $query->term . '%';
            $columns = $this->searchableColumns[$query->index];
            
            // Grouping OR conditions logically
            $firstColumn = array_shift($columns);
            $builder->where($firstColumn, 'LIKE', $term);
            
            foreach ($columns as $column) {
                $builder->orWhere($column, 'LIKE', $term);
            }
        }

        // 3. تطبيق الترتيب
        foreach ($query->sort as $column => $dir) {
            $builder->orderBy($column, $dir);
        }

        // 4. جلب العدد الإجمالي (بدون Limit) للـ Pagination
        // Note: For a true scalable system, this would be a separate count query
        $totalCount = count($builder->get());

        // 5. جلب البيانات المطلوبة
        $builder->limit($query->limit)->offset($query->offset);
        $items = $builder->get();

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        return new SearchResult($items, $totalCount, $executionTime);
    }
}