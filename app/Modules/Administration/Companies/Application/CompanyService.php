<?php
// Path: app/Modules/Administration/Companies/Application/CompanyService.php

declare(strict_types=1);

namespace App\Modules\Administration\Companies\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Administration\Companies\Domain\CompanyRepositoryInterface;
use App\Core\Tenant\Company;

/**
 * Enterprise Application Service: Company
 * مسؤول عن إنشاء الشركات الجديدة (Tenants) في النظام وإعدادها الأولي.
 */
class CompanyService
{
    protected CompanyRepositoryInterface $companyRepo;
    protected TransactionManager $transaction;

    public function __construct(CompanyRepositoryInterface $companyRepo, TransactionManager $transaction)
    {
        $this->companyRepo = $companyRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء شركة جديدة في النظام (Tenant Provisioning).
     *
     * @param array $data
     * @return Company
     * @throws BusinessException|\Throwable
     */
    public function createCompany(array $data): Company
    {
        return $this->transaction->execute(function () use ($data) {
            
            $data['status'] = 'active';
            $data['enforce_ip_whitelist'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');

            $companyId = $this->companyRepo->create($data);

            // في أنظمة הـ SaaS، هنا يتم إطلاق حدث (CompanyCreatedEvent) 
            // ليقوم النظام بإنشاء شجرة محاسبية افتراضية وإعدادات أولية للشركة الجديدة.

            $companyData = $this->companyRepo->findOrFail($companyId);

            return new Company($companyData);
        });
    }
}