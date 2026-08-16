<?php
// Path: app/Core/Documents/DocumentLock.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Lock Entity
 * يمثل سجل القفل الحالي لمستند معين، لمنع التعارض إذا حاول موظفان تعديله في نفس الوقت.
 */
class DocumentLock extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'document_id' => 'integer',
        'locked_by'   => 'integer', // المستخدم الذي فتح المستند للتعديل
        'locked_at'   => 'string',
        'expires_at'  => 'string',  // متى ينتهي القفل آلياً إذا نسي المستخدم إغلاقه
    ];

    /**
     * التحقق مما إذا كان القفل لا يزال سارياً.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $expires = strtotime((string) $this->getAttribute('expires_at'));
        return time() < $expires;
    }
}