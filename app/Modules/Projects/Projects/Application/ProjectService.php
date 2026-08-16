<?php
// Path: app/Modules/Projects/Projects/Application/ProjectService.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Application;

use App\Modules\Projects\Projects\Domain\Project;
use App\Modules\Projects\Projects\Domain\ProjectRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Project
 * يدير عملية إنشاء المشاريع وحالتها التشغيلية.
 */
class ProjectService
{
    protected ProjectRepositoryInterface $projectRepo;
    protected TransactionManager $transaction;

    public function __construct(ProjectRepositoryInterface $projectRepo, TransactionManager $transaction)
    {
        $this->projectRepo = $projectRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء مشروع جديد بكود تسلسلي آمن.
     *
     * @param array $data
     * @param int $companyId
     * @return Project
     * @throws \Throwable
     */
    public function createProject(array $data, int $companyId): Project
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['code'] = $this->projectRepo->generateProjectCode($companyId);
            $data['status'] = 'planned'; // Default status
            $data['budget'] = $data['budget'] ?? 0.00;

            $projectId = $this->projectRepo->create($data);

            $this->projectRepo->setTenantId($companyId);
            $projectData = $this->projectRepo->findOrFail($projectId);

            return new Project($projectData);
        });
    }
}