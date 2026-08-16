<?php
// Path: app/Modules/Fleet/Services/DriverService.php
declare(strict_types=1);

namespace App\Modules\Fleet\Services;

use App\Modules\Fleet\Repositories\DriverRepository;
use App\Core\Exceptions\BusinessException;

class DriverService
{
    protected DriverRepository $driverRepo;

    public function __construct(DriverRepository $driverRepo)
    {
        $this->driverRepo = $driverRepo;
    }

    public function registerDriver(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        
        if ($data['license_expiry'] < date('Y-m-d')) {
            throw new BusinessException("Cannot register a driver with an expired license.");
        }

        $data['is_active'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->driverRepo->create($data);
    }
}