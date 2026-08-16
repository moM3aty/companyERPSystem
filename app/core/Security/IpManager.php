<?php
// Path: app/Core/Security/IpManager.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise IP Security Manager
 * يتحقق مما إذا كان عنوان الـ IP مسموحاً به للنظام بالكامل أو لشركة معينة (IP Whitelisting).
 */
class IpManager
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق من الـ IP. يرمي استثناء إذا كان محظوراً.
     *
     * @param string $ipAddress
     * @param int|null $companyId إذا تم تمريره، سيتم فحص سياسة الشركة أيضاً.
     * @return void
     * @throws AuthorizationException
     */
    public function validateIp(string $ipAddress, ?int $companyId = null): void
    {
        // 1. التحقق من القائمة السوداء العامة للنظام (Global Blacklist)
        $blacklisted = $this->db->connection()->selectOne(
            "SELECT id FROM ip_security_rules WHERE ip_address = ? AND type = 'blacklist' AND (company_id IS NULL OR company_id = ?)",
            [$ipAddress, $companyId ?? 0]
        );

        if ($blacklisted) {
            throw new AuthorizationException("Access Denied: Your IP address ({$ipAddress}) is blocked by the security policy.", 403);
        }

        // 2. التحقق من القائمة البيضاء للشركة (إذا كانت الشركة تفعّل وضع التقييد بالـ IP)
        if ($companyId !== null) {
            $companyPolicy = $this->db->connection()->selectOne(
                "SELECT enforce_ip_whitelist FROM companies WHERE id = ?",
                [$companyId]
            );

            if ($companyPolicy && (int) $companyPolicy['enforce_ip_whitelist'] === 1) {
                $whitelisted = $this->db->connection()->selectOne(
                    "SELECT id FROM ip_security_rules WHERE ip_address = ? AND type = 'whitelist' AND company_id = ?",
                    [$ipAddress, $companyId]
                );

                if (!$whitelisted) {
                    throw new AuthorizationException("Access Denied: Your IP address is not authorized for this company network.", 403);
                }
            }
        }
    }
}