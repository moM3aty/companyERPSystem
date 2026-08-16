<?php
// Path: app/Core/Audit/AuditManager.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Http\Request;
use App\Core\Auth\AuthManager;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Audit Manager (Facade)
 * المنسق الرئيسي الذي يستقبل الطلبات لتسجيل الأنشطة.
 * يقوم تلقائياً بالتقاط الـ IP والـ UserAgent والـ Tenant لتقليل الكود في الـ Controllers.
 */
class AuditManager
{
    protected AuditRepository $repository;
    protected AuthManager $auth;
    protected TenantContext $tenant;
    protected ?Request $request = null;

    /**
     * AuditManager constructor.
     *
     * @param AuditRepository $repository
     * @param AuthManager $auth
     * @param TenantContext $tenant
     */
    public function __construct(AuditRepository $repository, AuthManager $auth, TenantContext $tenant)
    {
        $this->repository = $repository;
        $this->auth = $auth;
        $this->tenant = $tenant;
    }

    /**
     * تمرير الـ Request للحصول على بيانات المتصفح الدقيقة (يُستدعى عادة بواسطة Middleware).
     *
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * تسجيل حركة تغيير بيانات (Data Mutation).
     *
     * @param string $action ('created', 'updated', 'deleted')
     * @param string $entityType
     * @param int $entityId
     * @param array $oldValues
     * @param array $newValues
     * @return void
     */
    public function logMutation(string $action, string $entityType, int $entityId, array $oldValues = [], array $newValues = []): void
    {
        $data = $this->buildBaseLogData();
        
        $data['action']      = $action;
        $data['entity_type'] = $entityType;
        $data['entity_id']   = $entityId;
        $data['old_values']  = $oldValues;
        $data['new_values']  = $newValues;

        $this->repository->logChange($data);
    }

    /**
     * تسجيل نشاط عام في النظام (مثال: تصدير تقرير الضرائب).
     *
     * @param string $activityType
     * @param string $description
     * @param array $metadata
     * @return void
     */
    public function logActivity(string $activityType, string $description, array $metadata = []): void
    {
        $data = $this->buildBaseLogData();
        
        $data['activity_type'] = $activityType;
        $data['description']   = $description;
        $data['metadata']      = $metadata;

        $this->repository->logActivity($data);
    }

    /**
     * بناء البيانات المشتركة بين كل أنواع الـ Logs (تلقائياً دون إرهاق المطور).
     *
     * @return array
     */
    protected function buildBaseLogData(): array
    {
        $user = $this->auth->user();

        return [
            'company_id' => $this->tenant->getCompanyId() ?? ($user ? $user->companyId : null),
            'user_id'    => $user ? $user->id : null,
            'ip_address' => $this->request ? $this->request->server('REMOTE_ADDR', '127.0.0.1') : 'CLI/System',
            'user_agent' => $this->request ? $this->request->server('HTTP_USER_AGENT', 'Unknown') : 'Background Worker',
        ];
    }
}