<?php
// Path: app/Modules/Administration/Users/Domain/Events/UserCreatedEvent.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: UserCreatedEvent
 * يتم إطلاقه فور إنشاء مستخدم جديد لتتمكن باقي الموديولات (مثل الـ Notifications أو HR) من التفاعل.
 */
class UserCreatedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly string $email;
    public readonly string $username;

    /**
     * UserCreatedEvent constructor.
     *
     * @param int $userId
     * @param int $companyId
     * @param string $email
     * @param string $username
     */
    public function __construct(int $userId, int $companyId, string $email, string $username)
    {
        parent::__construct($userId); // $userId is the entityId
        $this->companyId = $companyId;
        $this->email = $email;
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id' => $this->companyId,
            'email'      => $this->email,
            'username'   => $this->username,
        ]);
    }
}