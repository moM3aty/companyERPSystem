<?php
// Path: app/Modules/Sales/Deliveries/Domain/DeliveryNoteRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Domain;

use App\Core\Contracts\RepositoryInterface;

interface DeliveryNoteRepositoryInterface extends RepositoryInterface
{
    public function generateDeliveryNumber(int $companyId): string;
    public function bulkInsertItems(int $deliveryId, array $items): void;
}