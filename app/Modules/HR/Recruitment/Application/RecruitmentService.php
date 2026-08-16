<?php
// Path: app/Modules/HR/Recruitment/Application/RecruitmentService.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\HR\Recruitment\Domain\JobOpeningRepositoryInterface;
use App\Modules\HR\Recruitment\Domain\ApplicantRepositoryInterface;

/**
 * Enterprise Application Service: Recruitment
 * يدير إنشاء الوظائف الشاغرة واستقبال المتقدمين بضمانات عدم التكرار.
 */
class RecruitmentService
{
    protected JobOpeningRepositoryInterface $jobRepo;
    protected ApplicantRepositoryInterface $applicantRepo;
    protected TransactionManager $transaction;

    public function __construct(
        JobOpeningRepositoryInterface $jobRepo,
        ApplicantRepositoryInterface $applicantRepo,
        TransactionManager $transaction
    ) {
        $this->jobRepo = $jobRepo;
        $this->applicantRepo = $applicantRepo;
        $this->transaction = $transaction;
    }

    public function createJobOpening(array $data, int $companyId, int $userId): int
    {
        $data['company_id'] = $companyId;
        $data['status']     = 'published'; // Publish immediately
        $data['created_by'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->jobRepo->create($data);
    }

    public function applyForJob(array $data, int $companyId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $jobId = (int) $data['job_opening_id'];
            $email = $data['email'];

            // 1. التحقق من صلاحية الوظيفة الشاغرة
            $this->jobRepo->setTenantId($companyId);
            $job = $this->jobRepo->findOrFail($jobId);

            if ($job['status'] !== 'published') {
                throw new BusinessException("This job opening is no longer accepting applications.", 422);
            }

            if (strtotime((string)$job['closing_date']) < time()) {
                throw new BusinessException("The application deadline for this job has passed.", 422);
            }

            // 2. التحقق من عدم التقدم المسبق
            if ($this->applicantRepo->hasAppliedBefore($jobId, $email)) {
                throw new BusinessException("An application with this email address has already been submitted for this job.", 409);
            }

            // 3. الحفظ
            $data['company_id'] = $companyId;
            $data['status']     = 'new';
            $data['rating']     = 0;
            $data['applied_at'] = date('Y-m-d H:i:s');
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->applicantRepo->create($data);
        });
    }
}