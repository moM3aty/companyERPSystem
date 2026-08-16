<?php
// Path: app/Core/Contracts/RepositoryInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Repository Interface
 * يفرض دوال الـ CRUD الأساسية لضمان توحيد نمط العمل في جميع مستودعات البيانات (Repositories) داخل الـ ERP.
 */
interface RepositoryInterface
{
    /**
     * تحديد الشركة (Tenant) الحالية لتنفيذ العمليات داخل نطاقها.
     *
     * @param int $tenantId
     * @return self
     */
    public function setTenantId(int $tenantId): self;

    public function all(array $columns = ['*']): array;
    public function find(int $id, array $columns = ['*']): mixed;
    public function findOrFail(int $id, array $columns = ['*']): mixed;
    public function paginate(int $perPage = 15, int $page = 1, array $columns = ['*']): array;
    public function create(array $data): int;
    public function update(int $id, array $data): int;
    public function delete(int $id): int;
}