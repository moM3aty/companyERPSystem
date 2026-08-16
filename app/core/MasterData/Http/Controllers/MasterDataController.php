<?php
// Path: app/Core/MasterData/Http/Controllers/MasterDataController.php

declare(strict_types=1);

namespace App\Core\MasterData\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\MasterData\MasterDataManager;

/**
 * Enterprise API Controller: Master Data
 * المغذي الرئيسي لواجهات المستخدم (Frontend). 
 * يوفر القوائم المنسدلة للعملات، الدول، الضرائب، ووحدات القياس بأداء فائق (Cached).
 */
class MasterDataController extends Controller
{
    protected MasterDataManager $masterData;
    protected TenantContext $tenant;

    public function __construct(MasterDataManager $masterData, TenantContext $tenant)
    {
        $this->masterData = $masterData;
        $this->tenant = $tenant;
        
        // مسار محمي ومتاح لأي مستخدم مسجل دخول داخل الشركة
        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * جلب العملات النشطة.
     */
    public function currencies(): JsonResponse
    {
        $currencies = $this->masterData->getActiveCurrencies();
        $data = array_map(fn($currency) => $currency->toArray(), $currencies);
        
        return ApiResponse::success($data);
    }

    /**
     * جلب الدول النشطة.
     */
    public function countries(): JsonResponse
    {
        $countries = $this->masterData->getActiveCountries();
        $data = array_map(fn($country) => $country->toArray(), $countries);
        
        return ApiResponse::success($data);
    }

    /**
     * جلب القوائم المرجعية (Lookups) بناءً على النوع (مثال: customer_categories).
     */
    public function lookups(string $type): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        
        $lookups = $this->masterData->getLookupsByType($type, $companyId);
        $data = array_map(fn($lookup) => $lookup->toArray(), $lookups);

        return ApiResponse::success($data);
    }

    /**
     * جلب سعر الصرف اللحظي للتسعير.
     */
    public function exchangeRate(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $baseId = (int) $request->query('base_currency_id');
        $targetId = (int) $request->query('target_currency_id');
        $date = $request->query('date', date('Y-m-d'));

        if (!$baseId || !$targetId) {
            return \App\Core\Api\ApiError::error("Base and Target Currency IDs are required.", 422);
        }

        $rate = $this->masterData->getExchangeRate($companyId, $baseId, $targetId, $date);

        return ApiResponse::success(['rate' => $rate, 'date' => $date]);
    }
}