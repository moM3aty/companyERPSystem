<?php
// Path: app/Domain/Enums/Status.php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Enterprise Enum: General Status
 */
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case ARCHIVED = 'archived';
}