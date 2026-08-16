<?php
// Path: app/Modules/Administration/Users/Infrastructure/UserRepository.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Administration\Users\Domain\User;
use App\Modules\Administration\Users\Domain\UserRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: User
 * التنفيذ الفعلي لمستودع بيانات المستخدمين باستخدام قاعدة البيانات.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $table = 'users';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    /**
     * UserRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email, int $companyId): ?User
    {
        $data = $this->newQuery()
                     ->where('email', '=', $email)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new User($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(int $userId, bool $isActive): int
    {
        return $this->update($userId, [
            'is_active' => $isActive ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findEntity(int $id): ?User
    {
        $data = $this->find($id);
        return $data ? new User($data) : null;
    }
}