<?php
// Path: app/Core/Search/SearchManager.php

declare(strict_types=1);

namespace App\Core\Search;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Search Manager (Facade)
 * الواجهة المركزية التي تتعامل معها الكنترولرات لتنفيذ عمليات البحث وتوجيهها للمزود المناسب.
 */
class SearchManager
{
    protected SearchProviderInterface $provider;
    protected IndexManager $indexManager;

    /**
     * SearchManager constructor.
     *
     * @param SearchProviderInterface $provider
     * @param IndexManager $indexManager
     */
    public function __construct(SearchProviderInterface $provider, IndexManager $indexManager)
    {
        $this->provider = $provider;
        $this->indexManager = $indexManager;
    }

    /**
     * تنفيذ استعلام بحث.
     *
     * @param SearchQuery $query
     * @return SearchResult
     * @throws BusinessException
     */
    public function execute(SearchQuery $query): SearchResult
    {
        if (empty($query->index)) {
            throw new BusinessException("Search Index cannot be empty.", 400);
        }

        return $this->provider->search($query);
    }

    /**
     * الوصول السريع لمدير الفهارس لمزامنة البيانات بعد عمليات الـ Insert/Update.
     *
     * @return IndexManager
     */
    public function indexer(): IndexManager
    {
        return $this->indexManager;
    }
}