    <?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Repositories/TaxRepository.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Repositories;

use App\Modules\Accounting\Domain\Repositories\TaxRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Persistence\Models\TaxModel;

class TaxRepository implements TaxRepositoryInterface
{
    private TaxModel $model;

    public function __construct()
    {
        $this->model = new TaxModel();
    }

    public function getAllActive(int $companyId): array
    {
        return $this->model->fetchAllActive($companyId);
    }

    public function findById(int $id, int $companyId): ?array { return null; }
    public function findByCode(string $code, int $companyId): ?array { return null; }
    public function create(array $data, int $companyId): int { return 0; }
    public function update(int $id, array $data, int $companyId): bool { return false; }
}