<?php
// Path: app/Modules/Inventory/Categories/Infrastructure/CategoryRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Categories\Domain\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected string $table = 'product_categories';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getTree(int $companyId): array
    {
        $flatCategories = $this->newQuery()
                               ->where('company_id', '=', $companyId)
                               ->orderBy('level', 'asc')
                               ->orderBy('name', 'asc')
                               ->get();

        return $this->buildNestedTree($flatCategories);
    }

    protected function buildNestedTree(array $flatCategories, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($flatCategories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildNestedTree($flatCategories, (int) $category['id']);
                
                $node = $category;
                $node['children'] = $children;
                
                $branch[] = $node;
            }
        }

        return $branch;
    }
}