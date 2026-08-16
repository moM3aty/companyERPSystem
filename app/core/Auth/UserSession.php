<?php
// Path: app/Core/Auth/UserSession.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: User Session
 * يمثل جلسة المستخدم الفعلية المخزنة في قاعدة البيانات. 
 * مفيد جداً لمراقبة الأجهزة النشطة وطرد المستخدمين (Session Revocation).
 */
class UserSession extends Entity
{
    protected array $casts = [
        'id'            => 'string',  // Session ID (Hash)
        'user_id'       => 'integer',
        'ip_address'    => 'string',
        'user_agent'    => 'string',
        'payload'       => 'string',  // Base64 Encoded Session Data
        'last_activity' => 'integer', // Unix Timestamp
    ];
}