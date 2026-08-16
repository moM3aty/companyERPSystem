<?php
// Path: app/Core/Organization/Division.php

declare(strict_types=1);

namespace App\Core\Organization;

/**
 * Enterprise Division Entity
 * القطاع الرئيسي (مثل: قطاع التكنولوجيا، قطاع المبيعات).
 * غالباً ما يكون في أعلى الشجرة التنظيمية (Level 0 أو 1).
 */
class Division extends OrganizationNode
{
    public function __construct(array $attributes = [])
    {
        $attributes['type'] = 'division';
        parent::__construct($attributes);
    }
}