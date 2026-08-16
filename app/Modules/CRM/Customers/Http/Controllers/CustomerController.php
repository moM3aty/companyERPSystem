<?php
// Path: app/Modules/CRM/Customers/Http/Controllers/CustomerController.php

declare(strict_types=1);

namespace App\Modules\CRM\Customers\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\Pagination;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\CRM\Customers\Application\CustomerService;
use App\Core\CRM\CustomerRepository;
use App\Modules\CRM\Customers\Http\Requests\StoreCustomerRequest;

/**
 * Enterprise API Controller: Customers (CRM)
 * إدارة العملاء وجهات الاتصال الخاصة بهم.
 */
class CustomerController extends Controller
{
    protected CustomerService $customerService;
    protected CustomerRepository $customerRepo;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        CustomerService $customerService,
        CustomerRepository $customerRepo,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->customerService = $customerService;
        $this->customerRepo = $customerRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;

        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('crm', 'customers', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->customerRepo->setTenantId($companyId);

        $pagination = Pagination::extract($request);

        $customers = $this->customerRepo->paginate($pagination['per_page'], $pagination['page']);

        return ApiResponse::success($customers);
    }

    public function store(Request $request, StoreCustomerRequest $validator): JsonResponse
    {
        $this->gate->authorize('crm', 'customers', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);
        
        $validatedData = $validator->validate($cleanData, $companyId);

        $customer = $this->customerService->createCustomer(
            $validatedData,
            $validatedData['contacts'] ?? [],
            $companyId
        );

        return ApiResponse::created($customer->toArray(), 'Customer and primary contacts created successfully.');
    }
}