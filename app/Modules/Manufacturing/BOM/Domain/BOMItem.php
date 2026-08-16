<?php
// Path: app/Modules/Manufacturing/BOM/Domain/BOMItem.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: BOM Item
 * Represents a single raw material or sub-assembly component required in the BOM.
 */
class BOMItem extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'bom_id'               => 'integer',
        'component_product_id' => 'integer', // The raw material
        'quantity'             => 'float',   // Required quantity per BOM batch
        'unit_id'              => 'integer', // Unit of measure for the component
        'scrap_percentage'     => 'float',   // Expected waste/scrap percentage (e.g., 5.0 for 5%)
    ];
}