<?php
// Path: app/Security/SecurityPolicies/Infrastructure/SecurityPolicyRepository.php

declare(strict_types=1);

namespace App\Security\SecurityPolicies\Infrastructure;

use App\Core\Database\DatabaseManager;
use App\Security\SecurityPolicies\Domain\SecurityPolicy;
use App\Security\SecurityPolicies\Domain\SecurityPolicyRepositoryInterface;
use App\Core\Cache\CacheManager;

class SecurityPolicyRepository implements SecurityPolicyRepositoryInterface
{
    protected DatabaseManager $db;
    protected CacheManager $cache;

    public function __construct(DatabaseManager $db, CacheManager $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function getPolicyForCompany(int $companyId): ?SecurityPolicy
    {
        $cacheKey = "security_policy_company_{$companyId}";

        $data = $this->cache->remember($cacheKey, 86400, function () use ($companyId) {
            return $this->db->connection()->selectOne(
                "SELECT * FROM security_policies WHERE company_id = ? LIMIT 1",
                [$companyId]
            );
        });

        // إذا لم توجد سياسة، نرجع سياسة افتراضية قوية لحماية النظام
        if (!$data) {
            return new SecurityPolicy([
                'company_id'                   => $companyId,
                'password_expiry_days'         => 90,
                'max_concurrent_sessions'      => 3,
                'session_idle_timeout_minutes' => 30,
                'enforce_mfa'                  => 0,
            ]);
        }

        return new SecurityPolicy($data);
    }
}