<?php
// Path: app/Core/Contracts/SearchInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Search Interface
 * Abstracts the search engine (SQL LIKE, Full-Text, or ElasticSearch).
 */
interface SearchInterface
{
    /**
     * Perform a search query against a specific entity/index.
     *
     * @param string $index The entity name (e.g., 'customers', 'products').
     * @param string $query The search term.
     * @param array $filters Additional filters.
     * @param int $limit Maximum results.
     * @return array Array of matching IDs or documents.
     */
    public function search(string $index, string $query, array $filters = [], int $limit = 20): array;

    /**
     * Index a document for searching.
     *
     * @param string $index
     * @param string|int $id
     * @param array $document
     * @return bool
     */
    public function indexDocument(string $index, string|int $id, array $document): bool;
}