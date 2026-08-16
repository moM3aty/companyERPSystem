<?php
// Path: app/Modules/Treasury/Accounts/Http/Controllers/TreasuryAccountController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Accounts\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Treasury\Accounts\Domain\TreasuryAccountRepositoryInterface;

/**
 * Enterprise API Controller: Treasury Accounts
 * يعرض الخزن والبنوك المتاحة للشركة.
 */
class TreasuryAccountController extends Controller
{
    protected TreasuryAccountRepositoryInterface $accountRepo;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(
        TreasuryAccountRepositoryInterface $accountRepo,
        Gate $gate,
        TenantContext $tenant
    ) {
        $this->accountRepo = $accountRepo;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->gate->authorize('treasury', 'accounts', 'view');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $this->accountRepo->setTenantId($companyId);

        $accounts = $this->accountRepo->all();

        return ApiResponse::success($accounts);
    }
}