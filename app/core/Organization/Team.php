<?php
// Path: app/Core/Organization/Team.php

declare(strict_types=1);

namespace App\Core\Organization;

/**
 * Enterprise Team Entity
 * الفريق (مثل: فريق واجهات المستخدم Front-End). يتبع لقسم معين (Department).
 */
class Team extends OrganizationNode
{
    public function __construct(array $attributes = [])
    {
        $attributes['type'] = 'team';
        parent::__construct($attributes);
    }
}