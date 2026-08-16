<?php
// Path: app/Core/Organization/LocationManager.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Location Manager
 * يدير مواقع الشركة (المباني/الفروع الفيزيائية).
 */
class LocationManager
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
    }

    /**
     * إضافة موقع جغرافي جديد للشركة.
     *
     * @param array $data
     * @return int
     */
    public function createLocation(array $data): int
    {
        $data['company_id'] = $this->tenant->requireTenant()->companyId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO locations ({$columns}) VALUES ({$placeholders})";
        
        $this->db->connection()->insert($sql, array_values($data));
        
        return (int) $this->db->connection()->lastInsertId();
    }
}