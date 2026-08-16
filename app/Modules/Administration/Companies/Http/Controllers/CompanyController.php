<?php
// Path: app/Modules/Administration/Companies/Http/Controllers/CompanyController.php

declare(strict_types=1);

namespace App\Modules\Administration\Companies\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Security\InputGuard;
use App\Modules\Administration\Companies\Application\CompanyService;
use App\Modules\Administration\Companies\Http\Requests\StoreCompanyRequest;

/**
 * Enterprise API Controller: Companies
 * مخصص لمدراء النظام (Super Admins) لإدارة الشركات المستأجرة في النظام (SaaS).
 */
class CompanyController extends Controller
{
    protected CompanyService $companyService;
    protected Gate $gate;
    protected InputGuard $inputGuard;

    public function __construct(
        CompanyService $companyService,
        Gate $gate,
        InputGuard $inputGuard
    ) {
        $this->companyService = $companyService;
        $this->gate = $gate;
        $this->inputGuard = $inputGuard;
        
        // لا نستخدم Tenant Middleware هنا لأن هذا الكنترولر يُستخدم قبل تحديد الشركة!
        $this->middleware(['api', 'auth']);
    }

    public function store(Request $request, StoreCompanyRequest $validator): JsonResponse
    {
        // التحقق من الصلاحية العليا (Super Admin)
        $this->gate->authorize('administration', 'companies', 'create');

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData);

        $company = $this->companyService->createCompany($validatedData);

        return ApiResponse::created($company->toArray(), 'New Company (Tenant) provisioned successfully.');
    }
}