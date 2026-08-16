<?php
// Path: app/Modules/APIPlatform/Keys/Application/ApiKeyManager.php

declare(strict_types=1);

namespace App\Modules\APIPlatform\Keys\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Security\HashManager;
use App\Core\Helpers\Str;
use App\Core\Exceptions\BusinessException;
use App\Modules\APIPlatform\Keys\Domain\ApiKey;

/**
 * Enterprise API Key Manager
 * يُنشئ مفاتيح الـ API بصيغة آمنة تماماً، حيث يظهر للعميل مرة واحدة فقط،
 * ويُخزن في قاعدة البيانات كـ Hash (مثل كلمات المرور).
 */
class ApiKeyManager
{
    protected DatabaseManager $db;
    protected HashManager $hash;

    public function __construct(DatabaseManager $db, HashManager $hash)
    {
        $this->db = $db;
        $this->hash = $hash;
    }

    /**
     * إصدار مفتاح API جديد.
     *
     * @param int $companyId
     * @param string $name
     * @param array $scopes
     * @param string|null $expiresAt
     * @param array $allowedIps
     * @param int $userId
     * @return array يحتوي على (المفتاح الصريح للعميل، والكائن المُنشأ)
     */
    public function createApiKey(int $companyId, string $name, array $scopes, ?string $expiresAt, array $allowedIps, int $userId): array
    {
        // 1. توليد مفتاح آمن وقوي 
        $plainKey = 'erk_' . Str::random(40); // erp key prefix

        // 2. تشفيره للحفظ (لا يمكن عكسه)
        $hashedKey = hash('sha256', $plainKey); // نستخدم SHA-256 للبحث السريع لاحقاً بدل Bcrypt

        // 3. حفظه
        $id = $this->db->connection()->insert(
            "INSERT INTO api_keys (company_id, name, key_hash, scopes, allowed_ips, expires_at, is_active, created_by, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)",
            [
                $companyId,
                $name,
                $hashedKey,
                json_encode($scopes),
                json_encode($allowedIps),
                $expiresAt,
                $userId,
                date('Y-m-d H:i:s')
            ]
        );

        return [
            'plain_key' => $plainKey, // هام: يُعرض مرة واحدة فقط!
            'api_key_id'=> $id
        ];
    }

    /**
     * التحقق من مفتاح الـ API الوارد في הـ Request.
     *
     * @param string $plainKey
     * @return ApiKey
     * @throws BusinessException
     */
    public function authenticate(string $plainKey): ApiKey
    {
        $hashedKey = hash('sha256', $plainKey);

        $row = $this->db->connection()->selectOne(
            "SELECT * FROM api_keys WHERE key_hash = ? LIMIT 1",
            [$hashedKey]
        );

        if (!$row) {
            throw new BusinessException("Invalid API Key.", 401);
        }

        $apiKey = new ApiKey($row);

        if (!$apiKey->isValid()) {
            throw new BusinessException("API Key is inactive or expired.", 401);
        }

        // تحديث آخر استخدام
        $this->db->connection()->update(
            "UPDATE api_keys SET last_used_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $apiKey->id]
        );

        return $apiKey;
    }
}