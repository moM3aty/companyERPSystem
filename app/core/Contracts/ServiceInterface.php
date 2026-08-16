<?php
// Path: app/Core/Contracts/ServiceInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Service Interface
 * A marker interface to enforce dependency injection contracts for domain services.
 */
interface ServiceInterface
{
    // Services encapsulate business logic. While they don't inherently require
    // identical methods like Repositories do, marking them with an interface 
    // aids in auto-discovery and binding within the DI Container.
}