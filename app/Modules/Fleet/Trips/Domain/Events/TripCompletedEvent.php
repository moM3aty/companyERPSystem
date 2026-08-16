<?php
// Path: app/Modules/Fleet/Trips/Domain/Events/TripCompletedEvent.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Trip Completed
 * يُطلق بعد انتهاء الرحلة.
 * المستمعون (Listeners) يمكن أن يضيفوا التكلفة المحاسبية كـ Journal Entry 
 * أو يحدّثوا بيانات تهالك الأصل (Asset Depreciation by mileage).
 */
class TripCompletedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $vehicleId;
    public readonly float $distanceCovered;
    public readonly float $tripCost;

    public function __construct(int $tripId, int $companyId, int $vehicleId, float $distanceCovered, float $tripCost)
    {
        parent::__construct($tripId);
        $this->companyId = $companyId;
        $this->vehicleId = $vehicleId;
        $this->distanceCovered = $distanceCovered;
        $this->tripCost = $tripCost;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'       => $this->companyId,
            'vehicle_id'       => $this->vehicleId,
            'distance_covered' => $this->distanceCovered,
            'trip_cost'        => $this->tripCost,
        ]);
    }
}