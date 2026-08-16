<?php
// Path: app/Core/Database/Model.php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Models\BaseModel;

/**
 * Enterprise Model (Alias)
 * فئة امتداد صريحة للـ BaseModel.
 */
abstract class Model extends BaseModel
{
    // Inherits casting, dirty-state tracking, and JSON serialization
}