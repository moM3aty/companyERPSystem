<?php
// Path: app/Modules/Fleet/Fuel/Application/FuelService.php

declare(strict_types=1);

namespace App\Modules\Fleet\Fuel\Application;

use App\Modules\Fleet\Fuel\Infrastructure\FuelRepository;
use App\Modules\Fleet\Vehicles\Infrastructure\VehicleRepository;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class FuelService
{
    protected FuelRepository $fuelRepo;
    protected VehicleRepository $vehicleRepo;
    protected TransactionManager $transaction;

    public function __construct(
        FuelRepository $fuelRepo, 
        VehicleRepository $vehicleRepo, 
        TransactionManager $transaction
    ) {
        $this->fuelRepo = $fuelRepo;
        $this->vehicleRepo = $vehicleRepo;
        $this->transaction = $transaction;
    }

    /**
     * تسجيل تعبئة وقود وتحديث عداد المركبة.
     */
    public function logFuel(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $vehicleId = (int) $data['vehicle_id'];
            $odometer = (float) $data['odometer_reading'];

            $this->vehicleRepo->setTenantId($companyId);
            $vehicle = $this->vehicleRepo->findOrFail($vehicleId);

            // التحقق من أن القراءة الجديدة أكبر من أو تساوي الحالية لتجنب التلاعب
            if ($odometer < (float) $vehicle['current_mileage']) {
                throw new BusinessException("Odometer reading cannot be less than the current vehicle mileage ({$vehicle['current_mileage']}).");
            }

            // 1. تحديث عداد السيارة
            if ($odometer > (float) $vehicle['current_mileage']) {
                $this->vehicleRepo->update($vehicleId, [
                    'current_mileage' => $odometer,
                    'updated_at'      => date('Y-m-d H:i:s')
                ]);
            }

            // 2. تسجيل فاتورة الوقود
            $data['company_id'] = $companyId;
            $data['created_by'] = $userId;
            $data['total_cost'] = (float) $data['liters'] * (float) $data['cost_per_liter'];
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->fuelRepo->create($data);

            // يمكن هنا إطلاق Event ليقوم الـ AccountingEngine بتسجيل قيد مصروف المحروقات
        });
    }
}