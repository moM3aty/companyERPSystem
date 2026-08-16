<?php
// Path: app/Modules/Fleet/Application/FleetService.php

declare(strict_types=1);

namespace App\Modules\Fleet\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Modules\Fleet\Vehicles\Domain\VehicleRepositoryInterface;
use App\Modules\Fleet\Trips\Domain\TripRepositoryInterface;
use App\Modules\Fleet\Trips\Domain\Events\TripCompletedEvent;

/**
 * Enterprise Application Service: Fleet Engine
 * يغلف منطق العمل الخاص بجدولة الرحلات واستهلاك السيارات لمنع التداخل أو الاستخدام المزدوج.
 */
class FleetService
{
    protected VehicleRepositoryInterface $vehicleRepo;
    protected TripRepositoryInterface $tripRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepo,
        TripRepositoryInterface $tripRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->vehicleRepo = $vehicleRepo;
        $this->tripRepo = $tripRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    /**
     * تسجيل مركبة جديدة في الأسطول.
     */
    public function createVehicle(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        $data['status'] = 'active';
        $data['current_mileage'] = $data['current_mileage'] ?? 0.0;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->vehicleRepo->create($data);
    }

    /**
     * بدء رحلة جديدة (تأكد من عدم وجود رحلة نشطة لنفس السيارة).
     */
    public function startTrip(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            $vehicleId = (int) $data['vehicle_id'];

            if ($this->tripRepo->hasActiveTrip($vehicleId, $companyId)) {
                throw new BusinessException("Vehicle ID [{$vehicleId}] is already in an active trip.", 409);
            }

            $data['company_id'] = $companyId;
            $data['status']     = 'in_progress';
            $data['created_by'] = $userId;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->tripRepo->create($data);
        });
    }

    /**
     * إنهاء الرحلة وتحديث عداد السيارة ورمي الحدث.
     */
    public function completeTrip(int $tripId, array $completionData, int $companyId): void
    {
        $this->transaction->execute(function () use ($tripId, $completionData, $companyId) {
            
            $this->tripRepo->setTenantId($companyId);
            $trip = $this->tripRepo->findOrFail($tripId);

            if ($trip['status'] === 'completed') {
                throw new BusinessException("Trip is already completed.");
            }

            $distance = (float) ($completionData['distance_covered'] ?? 0.0);
            $cost = (float) ($completionData['trip_cost'] ?? 0.0);

            // 1. تحديث الرحلة
            $this->tripRepo->update($tripId, [
                'status'           => 'completed',
                'end_time'         => date('Y-m-d H:i:s'),
                'distance_covered' => $distance,
                'fuel_consumed'    => (float) ($completionData['fuel_consumed'] ?? 0.0),
                'trip_cost'        => $cost,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // 2. تحديث قراءة عداد السيارة
            $vehicleId = (int) $trip['vehicle_id'];
            $this->vehicleRepo->setTenantId($companyId);
            $vehicle = $this->vehicleRepo->findOrFail($vehicleId);
            
            $newMileage = ((float) $vehicle['current_mileage']) + $distance;
            $this->vehicleRepo->update($vehicleId, [
                'current_mileage' => $newMileage,
                'updated_at'      => date('Y-m-d H:i:s')
            ]);

            // 3. إطلاق الحدث للنظام المحاسبي
            $this->eventBus->publish(new TripCompletedEvent($tripId, $companyId, $vehicleId, $distance, $cost));
        });
    }
}