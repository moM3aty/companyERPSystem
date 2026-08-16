<?php
// Path: app/Modules/Administration/Events/UserCreated.php

declare(strict_types=1);

namespace App\Modules\Administration\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Event: User Created
 * يمثل حدثاً يطلق من הـ Service عند إنشاء مستخدم جديد في النظام بنجاح.
 */
class UserCreated extends DomainEvent
{
    public readonly int $companyId;
    public readonly string $email;
    public readonly string $username;

    public function __construct(int $userId, int $companyId, string $email, string $username)
    {
        parent::__construct($userId);
        $this->companyId = $companyId;
        $this->email = $email;
        $this->username = $username;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id' => $this->companyId,
            'email'      => $this->email,
            'username'   => $this->username,
        ]);
    }
}