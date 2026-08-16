<?php
// Path: app/Modules/Administration/Users/Http/Controllers/UserController.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\Pagination;
use App\Core\Api\Filter;
use App\Core\Security\InputGuard;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Administration\Users\Application\UserService;
use App\Modules\Administration\Users\Domain\UserRepositoryInterface;
use App\Modules\Administration\Users\Http\Requests\StoreUserRequest;
use App\Modules\Administration\Users\Http\Requests\UpdateUserRequest;

/**
 * Enterprise API Controller: User Management
 * نقطة دخول الـ HTTP الخاصة بالمستخدمين. يطبق الـ ACL، يستقبل الطلبات، ويمررها للـ Service.
 */
class UserController extends Controller
{
    protected UserService $userService;
    protected UserRepositoryInterface $userRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        UserService $userService,
        UserRepositoryInterface $userRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->userService = $userService;
        $this->userRepo = $userRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        // تطبيق فلاتر الـ Middleware العامة للكنترولر
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * جلب قائمة المستخدمين (دعم الـ Pagination & Filtering).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('administration', 'users', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->userRepo->setTenantId($companyId);

        // استخراج إعدادات الـ API القياسية
        $paginationParams = Pagination::extract($request);
        $filters = Filter::extract($request, ['is_active', 'email']);

        // في النظام الكامل يتم تمرير الفلاتر للـ Repository
        $users = $this->userRepo->paginate($paginationParams['per_page'], $paginationParams['page']);

        // إرجاع الرد بصيغة متوافقة وموحدة
        return ApiResponse::success($users, 'Users retrieved successfully.');
    }

    /**
     * إنشاء مستخدم جديد.
     *
     * @param Request $request
     * @param StoreUserRequest $validator
     * @return JsonResponse
     */
    public function store(Request $request, StoreUserRequest $validator): JsonResponse
    {
        $this->gate->authorize('administration', 'users', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        // تنظيف البيانات من أكواد הـ HTML الخبيثة
        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        // التحقق من صحة البيانات (Validation)
        $validatedData = $validator->validate($cleanData, $companyId);

        // تمرير البيانات للـ Application Service لتنفيذ العمليات المعقدة
        $user = $this->userService->createUser($validatedData, $companyId);

        return ApiResponse::created($user->toArray(), 'User created successfully.');
    }

    /**
     * تحديث بيانات مستخدم.
     *
     * @param Request $request
     * @param UpdateUserRequest $validator
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, UpdateUserRequest $validator, int $id): JsonResponse
    {
        $this->gate->authorize('administration', 'users', 'update');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $id, $companyId);

        $user = $this->userService->updateUser($id, $validatedData, $companyId);

        return ApiResponse::success($user->toArray(), 'User updated successfully.');
    }

    /**
     * حذف مستخدم (Soft Delete).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->gate->authorize('administration', 'users', 'delete');
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $this->userRepo->setTenantId($companyId);
        $this->userRepo->findOrFail($id); // التحقق من وجوده

        $this->userRepo->delete($id);

        return ApiResponse::noContent();
    }
}