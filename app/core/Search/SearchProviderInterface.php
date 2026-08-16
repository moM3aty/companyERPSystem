<?php
// Path: app/Core/Search/SearchProviderInterface.php

declare(strict_types=1);

namespace App\Core\Search;

/**
 * Enterprise Search Provider Interface
 * عقد يلزم أي محرك بحث بآلية عمل محددة للاستعلامات.
 */
interface SearchProviderInterface
{
    /**
     * تنفيذ الاستعلام وإرجاع النتائج.
     *
     * @param SearchQuery $query
     * @return SearchResult
     */
    public function search(SearchQuery $query): SearchResult;
}