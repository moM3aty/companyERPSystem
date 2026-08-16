<?php
// Path: app/Core/Organization/Department.php

declare(strict_types=1);

namespace App\Core\Organization;

/**
 * Enterprise Department Entity
 * القسم الإداري (مثل: قسم تطوير البرمجيات). يتبع لقطاع معين (Division).
 */
class Department extends OrganizationNode
{
    public function __construct(array $attributes = [])
    {
        $attributes['type'] = 'department';
        parent::__construct($attributes);
    }
}