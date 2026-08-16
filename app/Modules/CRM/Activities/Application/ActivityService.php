<?php
// Path: app/Modules/CRM/Activities/Application/ActivityService.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Application;

use App\Modules\CRM\Activities\Domain\ActivityRepositoryInterface;
use App\Core\Database\TransactionManager;

class ActivityService
{
    protected ActivityRepositoryInterface $activityRepo;
    protected TransactionManager $transaction;

    public function __construct(ActivityRepositoryInterface $activityRepo, TransactionManager $transaction)
    {
        $this->activityRepo = $activityRepo;
        $this->transaction = $transaction;
    }

    public function logActivity(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $data['company_id'] = $companyId;
            $data['status']     = 'pending';
            $data['created_by'] = $userId;
            
            // إذا لم يتم تعيين مندوب، يتم تعيينها للمستخدم الحالي
            if (empty($data['assigned_to'])) {
                $data['assigned_to'] = $userId;
            }

            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->activityRepo->create($data);
        });
    }
}